<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * CRM › Customers.
 *
 * Ported from crmdemo/admin/customers.php + includes/tracker.php.
 * Query-builder only (no Eloquent models). `created_by` references staff.id;
 * the "assign to sales person" dropdown is populated from the staff table.
 */
class CustomerController extends Controller
{
    /** Staff roles offered in the "assign to sales person" dropdown. */
    private const SALES_ROLES = ['sales_rep', 'sales'];

    /** List all customers, with optional name/mobile search and rep filter. */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $rep    = (int) $request->query('rep', 0);

        $customers = DB::table('crm_customers as c')
            ->leftJoin('staff as u', 'c.created_by', '=', 'u.id')
            ->select('c.*', 'u.name as sales_name')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('c.name', 'like', "%{$search}%")
                      ->orWhere('c.mobile', 'like', "%{$search}%");
                });
            })
            ->when($rep > 0, fn ($q) => $q->where('c.created_by', $rep))
            ->orderByDesc('c.created_at')
            ->get();

        $repName = $rep > 0 ? (string) (DB::table('staff')->where('id', $rep)->value('name') ?? '') : '';

        return view('crm.customers.index', compact('customers', 'search', 'rep', 'repName'));
    }

    /** Show the "new customer" form. */
    public function create()
    {
        $salesUsers  = $this->salesUsers();
        $currentUser = Auth::guard('staff')->user();

        return view('crm.customers.create', [
            'salesUsers'    => $salesUsers,
            'currentUser'   => $currentUser,
            'generatedLink' => null,
            'share'         => null,
            'old'           => (object) ['name' => '', 'mobile' => '', 'notes' => '', 'created_by' => $currentUser->id],
        ]);
    }

    /** Store a new customer, generate the demo link + outreach messages. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'mobile'     => ['required', 'string', 'max:50'],
            'notes'      => ['nullable', 'string'],
            'created_by' => ['nullable', 'integer'],
        ]);

        $createdBy = (int) ($data['created_by'] ?? Auth::guard('staff')->id());
        // Guard against a tampered dropdown value pointing at a non-existent staff.
        if (! DB::table('staff')->where('id', $createdBy)->exists()) {
            $createdBy = (int) Auth::guard('staff')->id();
        }

        $token = $this->generateToken();

        DB::table('crm_customers')->insert([
            'name'        => $data['name'],
            'mobile'      => $data['mobile'],
            'token'       => $token,
            'created_by'  => $createdBy,
            'notes'       => $data['notes'] ?? null,
            'views_count' => 0,
            'created_at'  => now(),
        ]);

        $generatedLink = $this->watchLink($token);
        $repName       = DB::table('staff')->where('id', $createdBy)->value('name')
                         ?? Auth::guard('staff')->user()->name;

        $share = $this->buildOutreachMessages(
            ['name' => $data['name'], 'mobile' => $data['mobile']],
            $generatedLink,
            $repName
        );

        return view('crm.customers.create', [
            'salesUsers'    => $this->salesUsers(),
            'currentUser'   => Auth::guard('staff')->user(),
            'generatedLink' => $generatedLink,
            'share'         => $share,
            'old'           => (object) ['name' => '', 'mobile' => '', 'notes' => '', 'created_by' => $createdBy],
        ])->with('success', 'Customer created successfully.');
    }

    /** Delete a customer (cascades to views/sessions/segments via FKs). */
    public function destroy(int $customer)
    {
        DB::table('crm_customers')->where('id', $customer)->delete();

        return redirect()->route('crm.customers')->with('success', 'Customer deleted.');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Active staff eligible to own a customer (current user first, then sales team). */
    private function salesUsers()
    {
        return DB::table('staff')
            ->where('active', 1)
            ->whereIn('role', self::SALES_ROLES)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /** Unique, unguessable token for the public watch link. */
    private function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (DB::table('crm_customers')->where('token', $token)->exists());

        return $token;
    }

    /** Public watch URL for a customer token (landing page built in a later phase). */
    private function watchLink(string $token): string
    {
        return url('/crm/watch') . '?token=' . urlencode($token);
    }

    /** Company/brand name from settings, falling back to a sensible default. */
    private function companyName(): string
    {
        $name = DB::table('settings')->where('key', 'company_name')->value('value');
        $name = trim((string) $name);

        return $name !== '' ? $name : 'GetLead';
    }

    /**
     * Build ready-to-send, personalised WhatsApp + email outreach for a link.
     * Ported from crmdemo functions.php::buildOutreachMessages().
     */
    private function buildOutreachMessages(array $customer, string $link, string $repName = ''): array
    {
        $first   = Str::of($customer['name'] ?? '')->trim()->explode(' ')->first() ?: ($customer['name'] ?? '');
        $company = $this->companyName();
        $rep     = trim($repName);

        $whatsapp = "Hi {$first}! 👋\n\n"
            . "I wanted to share a quick demo that shows how {$company} can help you. 🎬\n\n"
            . "▶️ Watch it here (takes ~2 mins):\n{$link}\n\n"
            . "Take a look whenever you get a moment — happy to answer any questions! 😊";
        if ($rep !== '') {
            $whatsapp .= "\n\n— {$rep}, {$company}";
        }

        $subject = "🎬 A quick {$company} demo for you, {$first}";

        $body = "Hi {$first},\n\n"
            . "Thanks for your time! I wanted to share a short demo video that shows how {$company} can help you.\n\n"
            . "▶️ Watch it here:\n{$link}\n\n"
            . "It only takes a couple of minutes. If anything stands out — or you have any questions — just reply to this email and I'll be glad to help.\n\n"
            . "Looking forward to hearing what you think!\n\n"
            . "Warm regards,\n"
            . ($rep !== '' ? $rep . "\n" : '')
            . $company;

        $waDigits = preg_replace('/\D+/', '', $customer['mobile'] ?? '');
        if (strlen($waDigits) === 10) {
            $waDigits = '91' . $waDigits;
        }

        return [
            'whatsapp'      => $whatsapp,
            'email_subject' => $subject,
            'email_body'    => $body,
            'whatsapp_url'  => 'https://wa.me/' . $waDigits . '?text=' . rawurlencode($whatsapp),
            'email_url'     => 'mailto:?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($body),
        ];
    }
}
