<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrPayroll;
use App\Models\HrEmployeeSalary;
use App\Models\HrAttendance;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * HR › Payroll.
 *
 * Monthly payroll management — per-employee salary computation from attendance,
 * base-salary setup, "Process Payroll" (generate records for the month), and
 * payslip export. Adapted from hr_portal's admin payroll module.
 */

class PayrollController extends Controller
{
    /** Payroll list + stats for the selected month. */
    public function index(Request $request)
    {
        $month = $this->normaliseMonth($request->query('month'));
        [$start, $end, $label, $workingDays] = $this->monthMeta($month);

        $employees = HrEmployee::orderBy('full_name')->get();
        $records   = HrPayroll::where('month', $month)->whereNull('deleted_at')->get()->keyBy('employee_id');

        $rows = $employees->map(function ($e) use ($records, $start, $end, $workingDays) {
            [$hra, $ta] = $this->salaryPercents($e, $end);
            $rec = $records->get($e->id);
            if ($rec) {
                return [
                    'employee_id'   => $e->id,
                    'name'          => $e->full_name,
                    'base_salary'   => (float) $rec->base_salary,
                    'hra'           => $hra,
                    'ta'            => $ta,
                    'working_days'  => (int) $rec->working_days,
                    'present'       => (float) $rec->present_days,
                    'absent'        => (float) $rec->absent_days,
                    'leave'         => (float) $rec->leave_days,
                    'per_day'       => (float) $rec->per_day_salary,
                    'deduction'     => (float) $rec->deduction,
                    'net_salary'    => (float) $rec->net_salary,
                    'status'        => $rec->status,
                    'processed'     => in_array($rec->status, ['approved', 'paid'], true),
                ];
            }
            return $this->computeRow($e, $start, $end, $workingDays)
                + ['hra' => $hra, 'ta' => $ta, 'status' => 'pending', 'processed' => false];
        });

        $totWorking = $rows->sum('working_days');
        $stats = [
            'total_payroll' => $rows->sum('net_salary'),
            'processed'     => $rows->where('processed', true)->count(),
            'pending'       => $rows->where('processed', false)->count(),
            'avg_attendance'=> $totWorking > 0 ? round($rows->sum('present') / $totWorking * 100) : 0,
        ];

        $months = collect(range(0, 11))->map(function ($i) {
            $m = now()->startOfMonth()->subMonths($i);
            return ['value' => $m->format('Y-m'), 'label' => $m->format('F Y')];
        });

        return view('Hr.payroll.index', compact('rows', 'month', 'label', 'stats', 'employees', 'months'));
    }

    /** Set Base Salary (button) — always creates a new salary record. */
    public function setBaseSalary(Request $request)
    {
        $data = $this->validateSalary($request);
        $this->saveSalary($data, null);

        return response()->json(['ok' => true, 'message' => 'Base salary saved.']);
    }

    /** Edit Base Salary — updates the employee's record for the effective date, else creates one. */
    public function updateBaseSalary(Request $request)
    {
        $data = $this->validateSalary($request);
        $this->saveSalary($data, [
            'employee_id'    => $data['employee_id'],
            'effective_from' => $data['effective_from'],
        ]);

        return response()->json(['ok' => true, 'message' => 'Base salary updated.']);
    }

    /** Validation rules shared by set/update base salary. */
    private function validateSalary(Request $request): array
    {
        return $request->validate([
            'employee_id'   => ['required', 'integer', 'exists:hr_employees,id'],
            'base_salary'   => ['required', 'numeric', 'min:0'],
            'effective_from'=> ['required', 'date'],
            'hra'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ta'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /**
     * Persist a salary record + sync the employee's current salary.
     * $matchKeys null → create a fresh record; otherwise updateOrCreate on those keys.
     */
    private function saveSalary(array $data, ?array $matchKeys): void
    {
        $hraPct = $data['hra'] ?? 0;
        $taPct  = $data['ta'] ?? 0;
        $hra    = $data['base_salary'] * ($hraPct / 100);
        $ta     = $data['base_salary'] * ($taPct / 100);
        $salary = $data['base_salary'] + $hra + $ta;

        $values = [
            'base_salary'    => $data['base_salary'],
            'hra_percentage' => $hraPct,
            'ta_percentage'  => $taPct,
            'hra'            => $hra,
            'ta'             => $ta,
            'salary'         => $salary,
            'notes'          => $data['notes'] ?? null,
            'created_by'     => auth('staff')->id(),
        ];

        if ($matchKeys === null) {
            HrEmployeeSalary::create([
                'employee_id'    => $data['employee_id'],
                'effective_from' => $data['effective_from'],
            ] + $values);
        } else {
            HrEmployeeSalary::updateOrCreate($matchKeys, $values);
        }

        // Keep the employee's current salary in sync.
        HrEmployee::where('id', $data['employee_id'])->update([
            'salary' => $data['base_salary'],
            'hra'    => $hraPct,
            'ta'     => $taPct,
        ]);
    }

    /** Generate/refresh payroll records for every employee for the month. */
    public function process(Request $request)
    {
        $month = $this->normaliseMonth($request->input('month'));
        [$start, $end, $label, $workingDays] = $this->monthMeta($month);

        foreach (HrEmployee::all() as $e) {
            $c = $this->computeRow($e, $start, $end, $workingDays);
            HrPayroll::updateOrCreate(
                ['employee_id' => $e->id, 'month' => $month],
                [
                    'base_salary'    => $c['base_salary'],
                    'working_days'   => $c['working_days'],
                    'present_days'   => $c['present'],
                    'absent_days'    => $c['absent'],
                    'leave_days'     => $c['leave'],
                    'per_day_salary' => $c['per_day'],
                    'deduction'      => $c['deduction'],
                    'net_salary'     => $c['net_salary'],
                    'status'         => 'approved',
                    'processed_by'   => auth('staff')->id(),
                    'processed_at'   => now(),
                ]
            );
        }

        return redirect()->route('hr.payroll', ['month' => $month])->with('success', 'Payroll processed for ' . $label . '.');
    }

    /** CSV payslip export for the month. */
    public function export(Request $request): StreamedResponse
    {
        $month = $this->normaliseMonth($request->query('month'));
        [$start, $end, $label, $workingDays] = $this->monthMeta($month);
        $records = HrPayroll::where('month', $month)->whereNull('deleted_at')->get()->keyBy('employee_id');

        $rows = HrEmployee::orderBy('full_name')->get()->map(function ($e) use ($records, $start, $end, $workingDays) {
            $rec = $records->get($e->id);
            return $rec
                ? ['name' => $e->full_name, 'base' => $rec->base_salary, 'wd' => $rec->working_days, 'p' => $rec->present_days, 'a' => $rec->absent_days, 'l' => $rec->leave_days, 'pd' => $rec->per_day_salary, 'd' => $rec->deduction, 'net' => $rec->net_salary, 'status' => $rec->status]
                : (function ($c) { return ['name' => $c['name'], 'base' => $c['base_salary'], 'wd' => $c['working_days'], 'p' => $c['present'], 'a' => $c['absent'], 'l' => $c['leave'], 'pd' => $c['per_day'], 'd' => $c['deduction'], 'net' => $c['net_salary'], 'status' => 'pending']; })($this->computeRow($e, $start, $end, $workingDays));
        });

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Employee', 'Base Salary', 'Working Days', 'Present', 'Absent', 'Leave', 'Per Day Salary', 'Deduction', 'Net Salary', 'Status']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['name'], $r['base'], $r['wd'], $r['p'], $r['a'], $r['l'], $r['pd'], $r['d'], $r['net'], ucfirst($r['status'])]);
            }
            fclose($out);
        }, 'payslips_' . $month . '.csv', ['Content-Type' => 'text/csv']);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Compute a live payroll row for an employee within the month. */
    private function computeRow($e, Carbon $start, Carbon $end, int $workingDays): array
    {
        $base = $this->baseSalary($e, $end);

        $present = (float) HrAttendance::where('employee_id', $e->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'present')->count();
        $present += 0.5 * (float) HrAttendance::where('employee_id', $e->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'half_day')->count();
        $leave = (float) HrAttendance::where('employee_id', $e->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'on_leave')->count();

        $absent  = max(0, $workingDays - $present - $leave);
        $perDay  = $workingDays > 0 ? round($base / $workingDays, 2) : 0;
        $deduct  = round($perDay * $absent, 2);
        $net     = round($base - $deduct, 2);

        return [
            'employee_id'  => $e->id,
            'name'         => $e->full_name,
            'base_salary'  => $base,
            'working_days' => $workingDays,
            'present'      => $present,
            'absent'       => $absent,
            'leave'        => $leave,
            'per_day'      => $perDay,
            'deduction'    => $deduct,
            'net_salary'   => $net,
        ];
    }

    /** Latest effective base salary for an employee, falling back to the record's salary. */
    private function baseSalary($e, Carbon $end): float
    {
        $s = HrEmployeeSalary::where('employee_id', $e->id)
            ->whereNull('deleted_at')
            ->whereDate('effective_from', '<=', $end->toDateString())
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->value('base_salary');

        return (float) ($s ?? $e->salary ?? 0);
    }

    /** Latest effective [HRA%, TA%] for an employee, falling back to the employee record. */
    private function salaryPercents($e, Carbon $end): array
    {
        $rec = HrEmployeeSalary::where('employee_id', $e->id)
            ->whereNull('deleted_at')
            ->whereDate('effective_from', '<=', $end->toDateString())
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first(['hra_percentage', 'ta_percentage']);

        return [
            (float) ($rec->hra_percentage ?? $e->hra ?? 0),
            (float) ($rec->ta_percentage ?? $e->ta ?? 0),
        ];
    }

    /** Validate/normalise a 'YYYY-MM' month, defaulting to the current month. */
    private function normaliseMonth(?string $month): string
    {
        try {
            return Carbon::createFromFormat('Y-m', (string) $month)->format('Y-m');
        } catch (\Throwable) {
            return now()->format('Y-m');
        }
    }

    /** [start, end, label, workingDays] for a 'YYYY-MM' month. */
    private function monthMeta(string $month): array
    {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $wd = 0;
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            if (! $d->isWeekend()) {
                $wd++;
            }
        }

        return [$start, $end, $start->format('F Y'), $wd];
    }

}
