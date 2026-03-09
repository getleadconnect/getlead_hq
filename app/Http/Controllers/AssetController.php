<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCheckup;
use App\Models\AssetRepair;
use App\Models\QrCode;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    const TYPES    = ['laptop','mobile','monitor','printer','networking','furniture','keyboard','mouse','phone','ups','other'];
    const STATUSES = ['active','in_repair','maintenance','retired','lost'];

    private function staff()
    {
        return Auth::guard('staff')->user();
    }

    private function canEdit(): bool
    {
        return in_array($this->staff()->role, ['admin', 'secretary', 'finance']);
    }

    // ── Main View ─────────────────────────────────────────────────
    public function index()
    {
        $staffList = Staff::where('active', 1)->orderBy('name')->get(['id', 'name']);
        $canEdit   = $this->canEdit();
        $staffId   = $this->staff()->id;
        $types     = self::TYPES;
        $statuses  = self::STATUSES;

        return view('assets.index', compact('staffList', 'canEdit', 'staffId', 'types', 'statuses'));
    }

    // ── QR Lookup (public page, just needs auth) ──────────────────
    public function qrLookup(Request $request)
    {
        $code = trim($request->code ?? '');
        $qr   = null;
        if ($code) {
            $qr = QrCode::with(['asset.assignee'])->where('qr_code', $code)->first();
        }
        return response()->json(['ok' => true, 'qr' => $qr ? [
            'qr_code'    => $qr->qr_code,
            'asset_id'   => $qr->asset_id,
            'asset_tag'  => $qr->asset?->asset_tag,
            'asset_name' => $qr->asset?->name,
            'type'       => $qr->asset?->type,
            'status'     => $qr->asset?->status,
            'brand'      => $qr->asset?->brand,
            'model'      => $qr->asset?->model,
            'owner_name' => $qr->asset?->assignee?->name,
        ] : null]);
    }

    // ── API: Dashboard ────────────────────────────────────────────
    public function apiDashboard()
    {
        $staffId = $this->staff()->id;
        $today   = Carbon::today()->format('Y-m-d');

        $total          = Asset::count();
        $totalValue     = Asset::where('status', 'active')->sum('purchase_price');
        $repairCost     = AssetRepair::sum('cost');
        $inRepair       = Asset::where('status', 'in_repair')->count();
        $checkupsDue    = Asset::whereNotNull('next_checkup')->where('next_checkup', '<=', $today)->where('status', 'active')->count();
        $expiredWarranty = Asset::whereNotNull('warranty_expiry')->where('warranty_expiry', '<', $today)->where('status', 'active')->count();

        // My Assets
        $myAssets = Asset::with('assignee')
            ->where('assigned_to', $staffId)
            ->whereIn('status', ['active', 'in_repair'])
            ->orderBy('type')->orderBy('name')
            ->get()
            ->map(fn($a) => $this->assetRow($a));

        // By Type
        $byType = Asset::selectRaw('type, COUNT(*) as cnt')
            ->groupBy('type')->orderByDesc('cnt')->get()
            ->map(fn($r) => ['type' => $r->type, 'cnt' => $r->cnt]);

        // By Status
        $byStatus = Asset::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')->orderByDesc('cnt')->get()
            ->map(fn($r) => ['status' => $r->status, 'cnt' => $r->cnt]);

        // Assets per person
        $byOwner = Asset::join('staff', 'assets.assigned_to', '=', 'staff.id')
            ->selectRaw('staff.name, COUNT(*) as cnt')
            ->groupBy('staff.id', 'staff.name')
            ->orderByDesc('cnt')->get()
            ->map(fn($r) => ['name' => $r->name, 'cnt' => $r->cnt]);

        // Recent assignments
        $recentAssign = AssetAssignment::with(['asset', 'staff'])
            ->orderByDesc('assigned_at')->limit(8)->get()
            ->map(fn($r) => [
                'asset_tag'  => $r->asset?->asset_tag,
                'asset_name' => $r->asset?->name,
                'staff_name' => $r->staff?->name,
                'assigned_at'=> $r->assigned_at?->format('Y-m-d'),
            ]);

        // Checkups due list
        $checkupsDueList = Asset::with('assignee')
            ->whereNotNull('next_checkup')
            ->where('next_checkup', '<=', $today)
            ->where('status', 'active')
            ->orderBy('next_checkup')
            ->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'asset_tag'  => $a->asset_tag,
                'name'       => $a->name,
                'next_checkup'=> $a->next_checkup?->format('Y-m-d'),
                'last_checkup'=> $a->last_checkup?->format('Y-m-d'),
                'owner_name' => $a->assignee?->name,
            ]);

        // Needs attention
        $pendingRepairs = AssetRepair::with('asset')
            ->where('status', 'pending')->orderByDesc('date')->get()
            ->map(fn($r) => ['asset_tag' => $r->asset?->asset_tag, 'name' => $r->asset?->name, 'issue' => $r->issue]);

        $expiredWarrantyList = Asset::whereNotNull('warranty_expiry')
            ->where('warranty_expiry', '<', $today)
            ->where('status', 'active')
            ->orderByDesc('warranty_expiry')->limit(5)->get()
            ->map(fn($a) => ['asset_tag' => $a->asset_tag, 'name' => $a->name, 'warranty_expiry' => $a->warranty_expiry?->format('Y-m-d')]);

        $oldAssets = Asset::with('assignee')
            ->whereNotNull('purchase_date')
            ->where('purchase_date', '<', Carbon::now()->subYears(3)->format('Y-m-d'))
            ->where('status', 'active')->get()
            ->map(fn($a) => [
                'asset_tag'  => $a->asset_tag,
                'name'       => $a->name,
                'purchase_date' => $a->purchase_date?->format('Y-m-d'),
                'owner_name' => $a->assignee?->name,
            ]);

        return response()->json([
            'ok'               => true,
            'total'            => $total,
            'total_value'      => $totalValue,
            'repair_cost'      => $repairCost,
            'in_repair'        => $inRepair,
            'checkups_due'     => $checkupsDue,
            'expired_warranty' => $expiredWarranty,
            'my_assets'        => $myAssets,
            'by_type'          => $byType,
            'by_status'        => $byStatus,
            'by_owner'         => $byOwner,
            'recent_assign'    => $recentAssign,
            'checkups_due_list'=> $checkupsDueList,
            'pending_repairs'  => $pendingRepairs,
            'expired_warranty_list' => $expiredWarrantyList,
            'old_assets'       => $oldAssets,
        ]);
    }

    // ── API: Asset List ───────────────────────────────────────────
    public function apiList(Request $request)
    {
        $q = Asset::with('assignee');
        if ($type = $request->ftype)     $q->where('type', $type);
        if ($status = $request->fstatus) $q->where('status', $status);
        if ($owner = $request->fowner)   $q->where('assigned_to', $owner);
        if ($s = $request->q) {
            $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('brand', 'like', "%$s%")
                  ->orWhere('model', 'like', "%$s%")
                  ->orWhere('serial_number', 'like', "%$s%")
                  ->orWhere('asset_tag', 'like', "%$s%");
            });
        }
        $perPage = 25;
        $page    = max(1, (int) ($request->p ?? 1));
        $total   = (clone $q)->count();
        $assets  = $q->orderByDesc('id')
                     ->skip(($page - 1) * $perPage)
                     ->take($perPage)
                     ->get()
                     ->map(fn($a) => $this->assetRow($a));
        return response()->json([
            'ok'     => true,
            'assets' => $assets,
            'total'  => $total,
            'pages'  => (int) ceil($total / $perPage),
            'page'   => $page,
        ]);
    }

    // ── API: Asset Detail ─────────────────────────────────────────
    public function apiDetail(int $id)
    {
        $a = Asset::with(['assignee', 'qrCode'])->findOrFail($id);

        $assignments = AssetAssignment::with('staff')
            ->where('asset_id', $id)->orderByDesc('assigned_at')->get()
            ->map(fn($r) => [
                'staff_name'  => $r->staff?->name,
                'assigned_at' => $r->assigned_at?->format('Y-m-d'),
                'returned_at' => $r->returned_at?->format('Y-m-d'),
                'notes'       => $r->notes,
            ]);

        $repairs = AssetRepair::where('asset_id', $id)->orderByDesc('date')->get()
            ->map(fn($r) => [
                'id'     => $r->id,
                'date'   => $r->date?->format('Y-m-d'),
                'issue'  => $r->issue,
                'cost'   => $r->cost,
                'vendor' => $r->vendor,
                'status' => $r->status,
                'notes'  => $r->notes,
            ]);

        $checkups = AssetCheckup::with('checker')
            ->where('asset_id', $id)->orderByDesc('checkup_date')->get()
            ->map(fn($c) => [
                'checkup_date' => $c->checkup_date?->format('Y-m-d'),
                'checker_name' => $c->checker?->name,
                'conditions'   => $c->conditions,
                'remarks'      => $c->remarks,
            ]);

        return response()->json([
            'ok'          => true,
            'asset'       => $this->assetRow($a, true),
            'assignments' => $assignments,
            'repairs'     => $repairs,
            'checkups'    => $checkups,
        ]);
    }

    // ── API: Save Asset ───────────────────────────────────────────
    public function apiSave(Request $request)
    {
        if (!$this->canEdit()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);

        $data = $request->all();
        $id   = $data['id'] ?? null;

        $fields = [
            'name'             => trim($data['name']),
            'type'             => $data['type'],
            'brand'            => trim($data['brand'] ?? ''),
            'model'            => trim($data['model'] ?? ''),
            'serial_number'    => trim($data['serial_number'] ?? ''),
            'purchase_date'    => $data['purchase_date'] ?: null,
            'purchase_price'   => $data['purchase_price'] ?: null,
            'vendor'           => trim($data['vendor'] ?? ''),
            'assigned_to'      => $data['assigned_to'] ?: null,
            'status'           => $data['status'],
            'warranty_expiry'  => $data['warranty_expiry'] ?: null,
            'notes'            => trim($data['notes'] ?? ''),
            'remarks'          => trim($data['remarks'] ?? ''),
            'checkup_interval' => (int)($data['checkup_interval'] ?? 90) ?: 90,
        ];

        if ($id) {
            $asset    = Asset::findOrFail($id);
            $oldOwner = $asset->assigned_to;
            $asset->update($fields);

            // Handle assignment change
            if ($oldOwner != $fields['assigned_to']) {
                if ($oldOwner) {
                    AssetAssignment::where('asset_id', $id)
                        ->where('staff_id', $oldOwner)
                        ->whereNull('returned_at')
                        ->update(['returned_at' => today()->format('Y-m-d')]);
                }
                if ($fields['assigned_to']) {
                    AssetAssignment::create([
                        'asset_id'    => $id,
                        'staff_id'    => $fields['assigned_to'],
                        'assigned_at' => today()->format('Y-m-d'),
                    ]);
                }
            }
            return response()->json(['ok' => true, 'msg' => 'Asset updated', 'id' => $id]);
        } else {
            $fields['asset_tag'] = Asset::nextTag();
            $asset = Asset::create($fields);
            if ($fields['assigned_to']) {
                AssetAssignment::create([
                    'asset_id'    => $asset->id,
                    'staff_id'    => $fields['assigned_to'],
                    'assigned_at' => today()->format('Y-m-d'),
                ]);
            }
            return response()->json(['ok' => true, 'msg' => 'Asset added', 'id' => $asset->id]);
        }
    }

    // ── API: Delete Asset ─────────────────────────────────────────
    public function apiDelete(Request $request)
    {
        if (!$this->canEdit()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        $asset = Asset::findOrFail($request->id);
        AssetRepair::where('asset_id', $asset->id)->delete();
        AssetAssignment::where('asset_id', $asset->id)->delete();
        AssetCheckup::where('asset_id', $asset->id)->delete();
        QrCode::where('asset_id', $asset->id)->update(['asset_id' => null, 'mapped_at' => null, 'mapped_by' => null]);
        $asset->delete();
        return response()->json(['ok' => true]);
    }

    // ── API: Add Repair ───────────────────────────────────────────
    public function apiRepair(Request $request)
    {
        if (!$this->canEdit()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        AssetRepair::create([
            'asset_id' => $request->asset_id,
            'date'     => $request->date,
            'issue'    => trim($request->issue),
            'cost'     => $request->cost ?? 0,
            'vendor'   => trim($request->vendor ?? ''),
            'status'   => $request->status,
            'notes'    => trim($request->notes ?? ''),
        ]);
        return response()->json(['ok' => true, 'msg' => 'Repair logged']);
    }

    // ── API: Log Checkup ──────────────────────────────────────────
    public function apiCheckup(Request $request)
    {
        $assetId  = $request->asset_id;
        $date     = $request->checkup_date ?: today()->format('Y-m-d');
        $staffId  = $this->staff()->id;

        AssetCheckup::create([
            'asset_id'     => $assetId,
            'checked_by'   => $staffId,
            'checkup_date' => $date,
            'conditions'   => $request->conditions ?? 'good',
            'remarks'      => trim($request->remarks ?? ''),
        ]);

        $asset    = Asset::findOrFail($assetId);
        $interval = $asset->checkup_interval ?: 90;
        $next     = Carbon::parse($date)->addDays($interval)->format('Y-m-d');
        $asset->update(['last_checkup' => $date, 'next_checkup' => $next]);

        return response()->json(['ok' => true, 'msg' => 'Checkup logged']);
    }

    // ── API: QR Codes ─────────────────────────────────────────────
    public function apiQrCodes(Request $request)
    {
        $filter = $request->qf ?? 'all';
        $page   = max(1, (int)($request->p ?? 1));
        $perPage = 50;

        $q = QrCode::with('asset');
        if ($filter === 'unmapped') $q->whereNull('asset_id');
        elseif ($filter === 'mapped') $q->whereNotNull('asset_id');

        $total  = $q->count();
        $codes  = $q->orderBy('id')->skip(($page - 1) * $perPage)->take($perPage)->get()
            ->map(fn($c) => [
                'id'         => $c->id,
                'qr_code'    => $c->qr_code,
                'asset_id'   => $c->asset_id,
                'asset_tag'  => $c->asset?->asset_tag,
                'asset_name' => $c->asset?->name,
            ]);

        return response()->json([
            'ok'         => true,
            'codes'      => $codes,
            'total'      => $total,
            'pages'      => (int)ceil($total / $perPage),
            'page'       => $page,
        ]);
    }

    // ── API: QR Map ───────────────────────────────────────────────
    public function apiQrMap(Request $request)
    {
        if (!$this->canEdit()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        $qrCode  = trim($request->qr_code);
        $assetId = $request->asset_id;
        // Remove old mapping for this asset
        QrCode::where('asset_id', $assetId)->update(['asset_id' => null, 'mapped_at' => null, 'mapped_by' => null]);
        QrCode::where('qr_code', $qrCode)->update([
            'asset_id'  => $assetId,
            'mapped_at' => now(),
            'mapped_by' => $this->staff()->id,
        ]);
        return response()->json(['ok' => true, 'msg' => $qrCode . ' mapped successfully']);
    }

    // ── API: QR Unmap ─────────────────────────────────────────────
    public function apiQrUnmap(Request $request)
    {
        if (!$this->canEdit()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        QrCode::where('qr_code', trim($request->qr_code))->update([
            'asset_id'  => null,
            'mapped_at' => null,
            'mapped_by' => null,
        ]);
        return response()->json(['ok' => true]);
    }

    // ── API: QR Generate ─────────────────────────────────────────
    public function apiQrGenerate(Request $request)
    {
        if (!$this->canEdit()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        $gen = min(200, max(1, (int)($request->count ?? 50)));
        $maxNum = (int) QrCode::selectRaw("MAX(CAST(SUBSTRING(qr_code, 4) AS UNSIGNED)) as mx")
            ->where('qr_code', 'like', 'GL-%')->value('mx');

        $inserted = 0;
        for ($i = $maxNum + 1; $i <= $maxNum + $gen; $i++) {
            $code = 'GL-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            if (!QrCode::where('qr_code', $code)->exists()) {
                QrCode::create(['qr_code' => $code]);
                $inserted++;
            }
        }
        return response()->json(['ok' => true, 'generated' => $inserted]);
    }

    // ── API: QR Map Data (for map form) ───────────────────────────
    public function apiQrMapData()
    {
        $unmappedQR = QrCode::whereNull('asset_id')->orderBy('id')->pluck('qr_code');
        $unmappedAssets = Asset::whereDoesntHave('qrCode')
            ->orWhereHas('qrCode', fn($q) => $q->whereNull('asset_id'))
            ->select('id', 'asset_tag', 'name')->orderBy('asset_tag')->get();

        $recentMaps = QrCode::with(['asset', 'mapper'])
            ->whereNotNull('asset_id')
            ->orderByDesc('mapped_at')->limit(20)->get()
            ->map(fn($q) => [
                'qr_code'    => $q->qr_code,
                'asset_tag'  => $q->asset?->asset_tag,
                'asset_name' => $q->asset?->name,
                'mapper'     => $q->mapper?->name,
                'mapped_at'  => $q->mapped_at,
            ]);

        return response()->json([
            'ok'              => true,
            'unmapped_qr'     => $unmappedQR,
            'unmapped_assets' => $unmappedAssets,
            'recent_maps'     => $recentMaps,
        ]);
    }

    // ── Helper: format an asset row ───────────────────────────────
    private function assetRow(Asset $a, bool $full = false): array
    {
        $today = Carbon::today()->format('Y-m-d');
        $row = [
            'id'               => $a->id,
            'asset_tag'        => $a->asset_tag,
            'name'             => $a->name,
            'type'             => $a->type,
            'brand'            => $a->brand,
            'model'            => $a->model,
            'serial_number'    => $a->serial_number,
            'purchase_date'    => $a->purchase_date?->format('Y-m-d'),
            'purchase_price'   => $a->purchase_price,
            'vendor'           => $a->vendor,
            'assigned_to'      => $a->assigned_to,
            'owner_name'       => $a->assignee?->name,
            'status'           => $a->status,
            'warranty_expiry'  => $a->warranty_expiry?->format('Y-m-d'),
            'notes'            => $a->notes,
            'remarks'          => $a->remarks,
            'checkup_interval' => $a->checkup_interval,
            'last_checkup'     => $a->last_checkup?->format('Y-m-d'),
            'next_checkup'     => $a->next_checkup?->format('Y-m-d'),
            'checkup_due'      => $a->next_checkup && $a->next_checkup->format('Y-m-d') <= $today,
            'warranty_expired' => $a->warranty_expiry && $a->warranty_expiry->format('Y-m-d') < $today,
        ];
        if ($full) {
            $row['qr_code'] = $a->qrCode?->qr_code;
        }
        return $row;
    }
}
