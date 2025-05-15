<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessKey;
use App\Models\ValidationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;

class LicenseController extends Controller
{
    /**
     * Display a listing of all access keys
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AccessKey::query();

        // Handle search and filtering
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('owner_email', 'like', "%{$search}%");
            });
        }

        if ($request->has('tier')) {
            $query->where('tier', $request->tier);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('revoked', false)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', Carbon::now());
                    });
            } elseif ($request->status === 'expired') {
                $query->where('revoked', false)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<', Carbon::now());
            } elseif ($request->status === 'revoked') {
                $query->where('revoked', true);
            }
        }

        // Sort options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $keys = $query->paginate(20);

        // Get stats for dashboard
        $stats = [
            'total' => AccessKey::count(),
            'active' => AccessKey::where('revoked', false)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', Carbon::now());
                })->count(),
            'expired' => AccessKey::where('revoked', false)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', Carbon::now())
                ->count(),
            'revoked' => AccessKey::where('revoked', true)->count(),
        ];

        $tiers = AccessKey::distinct('tier')->pluck('tier');

        return view('admin.access-keys.index', compact('keys', 'stats', 'tiers'));
    }

    /**
     * Show form to create a new access key
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.access-keys.create');
    }

    /**
     * Store a newly created access key
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'tier' => 'required|string',
            'max_domains' => 'required|integer|min:1',
            'allowed_domains' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'features' => 'nullable|array',
            'metadata' => 'nullable|array',
            'allow_auto_registration' => 'boolean',
            'allow_localhost' => 'boolean',
            'grace_period_hours' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process domains from text to array
        $allowedDomains = null;
        if ($request->filled('allowed_domains')) {
            $domains = preg_split('/\r\n|\r|\n/', $request->allowed_domains);
            $domains = array_map('trim', $domains);
            $domains = array_filter($domains);
            $allowedDomains = array_values($domains);
        }

        // Create the new access key
        $accessKey = new AccessKey;
        $accessKey->key = Str::uuid();
        $accessKey->owner_name = $request->owner_name;
        $accessKey->owner_email = $request->owner_email;
        $accessKey->tier = $request->tier;
        $accessKey->max_domains = $request->max_domains;
        $accessKey->allowed_domains = $allowedDomains;
        $accessKey->expires_at = $request->expires_at;
        $accessKey->features = $request->features;
        $accessKey->metadata = $request->metadata;
        $accessKey->allow_auto_registration = $request->has('allow_auto_registration');
        $accessKey->allow_localhost = $request->has('allow_localhost');
        $accessKey->grace_period_hours = $request->filled('grace_period_hours') ? $request->grace_period_hours : 72;
        $accessKey->save();

        return redirect()->route('admin.access-keys.index')
            ->with('success', 'Access key created successfully.');
    }

    /**
     * Display the specified access key
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $key = AccessKey::findOrFail($id);

        // Get recent validation logs
        $recentLogs = ValidationLog::where('access_key', $key->key)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Get active domains
        $activeDomains = $key->active_domains;

        // Get usage statistics
        $usageStats = [
            'total_validations' => ValidationLog::where('access_key', $key->key)->count(),
            'successful_validations' => ValidationLog::where('access_key', $key->key)
                ->where('status', 'valid')
                ->count(),
            'failed_validations' => ValidationLog::where('access_key', $key->key)
                ->whereNotIn('status', ['valid', 'domain_registered'])
                ->count(),
            'auto_registrations' => ValidationLog::where('access_key', $key->key)
                ->where('auto_registered', true)
                ->count(),
        ];

        // Check status
        $status = 'active';
        if ($key->revoked) {
            $status = 'revoked';
        } elseif ($key->isExpired()) {
            $status = 'expired';
        }

        return view('admin.access-keys.show', compact('key', 'recentLogs', 'activeDomains', 'usageStats', 'status'));
    }

    /**
     * Show form for editing the specified access key
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $key = AccessKey::findOrFail($id);

        // Format domains as text for textarea
        $domainsText = '';
        if (! empty($key->allowed_domains) && is_array($key->allowed_domains)) {
            $domainsText = implode("\n", $key->allowed_domains);
        }

        return view('admin.access-keys.edit', compact('key', 'domainsText'));
    }

    /**
     * Update the specified access key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $key = AccessKey::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'tier' => 'required|string',
            'max_domains' => 'required|integer|min:1',
            'allowed_domains' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'features' => 'nullable|array',
            'metadata' => 'nullable|array',
            'allow_auto_registration' => 'boolean',
            'allow_localhost' => 'boolean',
            'grace_period_hours' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process domains from text to array
        $allowedDomains = null;
        if ($request->filled('allowed_domains')) {
            $domains = preg_split('/\r\n|\r|\n/', $request->allowed_domains);
            $domains = array_map('trim', $domains);
            $domains = array_filter($domains);
            $allowedDomains = array_values($domains);
        }

        // Update the access key
        $key->owner_name = $request->owner_name;
        $key->owner_email = $request->owner_email;
        $key->tier = $request->tier;
        $key->max_domains = $request->max_domains;
        $key->allowed_domains = $allowedDomains;
        $key->expires_at = $request->expires_at;
        $key->features = $request->features;
        $key->metadata = $request->metadata;
        $key->allow_auto_registration = $request->boolean('allow_auto_registration');
        $key->allow_localhost = $request->boolean('allow_localhost');
        $key->grace_period_hours = $request->filled('grace_period_hours') ? $request->grace_period_hours : 72;
        $key->save();

        return redirect()->route('admin.access-keys.show', $key->id)
            ->with('success', 'Access key updated successfully.');
    }

    /**
     * Remove the specified access key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $key = AccessKey::findOrFail($id);

        // Permanently delete the key and all related logs
        ValidationLog::where('access_key', $key->key)->delete();
        $key->delete();

        return redirect()->route('admin.access-keys.index')
            ->with('success', 'Access key deleted successfully with all related logs.');
    }

    /**
     * Revoke the specified access key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(Request $request, $id)
    {
        $key = AccessKey::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'revocation_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $key->revoked = true;
        $key->revocation_reason = $request->revocation_reason;
        $key->save();

        return redirect()->route('admin.access-keys.show', $key->id)
            ->with('success', 'Access key revoked successfully.');
    }

    /**
     * Restore a revoked access key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $key = AccessKey::findOrFail($id);

        $key->revoked = false;
        $key->revocation_reason = null;
        $key->save();

        return redirect()->route('admin.access-keys.show', $key->id)
            ->with('success', 'Access key restored successfully.');
    }

    /**
     * Reset domain tracking for a key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetDomains($id)
    {
        $key = AccessKey::findOrFail($id);

        // Mark all validations as inactive for domain counting
        ValidationLog::where('access_key', $key->key)
            ->update(['reset_domains' => true]);

        return redirect()->route('admin.access-keys.show', $key->id)
            ->with('success', 'Domain tracking has been reset for this key.');
    }

    /**
     * Extend the validity of an access key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extendValidity(Request $request, $id)
    {
        $key = AccessKey::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'extend_months' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $months = $request->extend_months;

        // If key already expired, extend from current date
        if ($key->isExpired()) {
            $key->expires_at = Carbon::now()->addMonths($months);
        } else {
            // Otherwise extend from current expiration date
            $expiryDate = $key->expires_at ?? Carbon::now();
            $key->expires_at = Carbon::parse($expiryDate)->addMonths($months);
        }

        $key->save();

        return redirect()->route('admin.access-keys.show', $key->id)
            ->with('success', "Access key validity extended by {$months} months.");
    }

    /**
     * Manage allowed domains for a key
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manageDomains(Request $request, $id)
    {
        $key = AccessKey::findOrFail($id);

        $action = $request->input('action');
        $domain = $request->input('domain');

        if ($action === 'add' && $request->filled('new_domain')) {
            $newDomain = trim($request->input('new_domain'));
            $key->addAllowedDomain($newDomain);

            return redirect()->route('admin.access-keys.show', $key->id)
                ->with('success', "Domain '{$newDomain}' added successfully.");
        } elseif ($action === 'remove' && $domain) {
            $key->removeAllowedDomain($domain);

            return redirect()->route('admin.access-keys.show', $key->id)
                ->with('success', "Domain '{$domain}' removed successfully.");
        }

        return redirect()->route('admin.access-keys.show', $key->id)
            ->with('error', 'Invalid domain management action.');
    }
}
