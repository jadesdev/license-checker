<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessKey;
use App\Models\ValidationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $query = AccessKey::query();

        // Apply filters
        if ($request->has('tier')) {
            $query->where('tier', $request->tier);
        }

        if ($request->has('revoked')) {
            $query->where('revoked', filter_var($request->revoked, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('expired')) {
            if (filter_var($request->expired, FILTER_VALIDATE_BOOLEAN)) {
                $query->whereNotNull('expires_at')->where('expires_at', '<', Carbon::now());
            } else {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                });
            }
        }

        $keys = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($keys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'allowed_domains' => 'nullable|array',
            'max_domains' => 'nullable|integer|min:1',
            'tier' => 'required|string|in:standard,premium,enterprise',
            'features' => 'nullable|array',
            'metadata' => 'nullable|array',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = new AccessKey();
        $key->key = AccessKey::generateKey();
        $key->owner_name = $request->owner_name;
        $key->owner_email = $request->owner_email;
        $key->allowed_domains = $request->has('allowed_domains') ? json_encode($request->allowed_domains) : null;
        $key->max_domains = $request->max_domains ?? 1;
        $key->tier = $request->tier;
        $key->features = $request->features;
        $key->metadata = $request->metadata;
        $key->expires_at = $request->expires_at;
        $key->save();

        return response()->json($key, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $key = AccessKey::findOrFail($id);

        // Add additional stats
        $key->active_domains_count = $key->validationLogs()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('status', 'valid')
            ->distinct('domain')
            ->count('domain');

        $key->active_domains = $key->validationLogs()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('status', 'valid')
            ->distinct('domain')
            ->pluck('domain')
            ->toArray();

        $key->recent_validations = $key->validationLogs()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($key);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $key = AccessKey::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'allowed_domains' => 'nullable|array',
            'max_domains' => 'nullable|integer|min:1',
            'tier' => 'nullable|string|in:standard,premium,enterprise',
            'features' => 'nullable|array',
            'metadata' => 'nullable|array',
            'expires_at' => 'nullable|date',
            'revoked' => 'nullable|boolean',
            'revocation_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update fields if they're provided
        if ($request->has('owner_name')) $key->owner_name = $request->owner_name;
        if ($request->has('owner_email')) $key->owner_email = $request->owner_email;
        if ($request->has('allowed_domains')) $key->allowed_domains = json_encode($request->allowed_domains);
        if ($request->has('max_domains')) $key->max_domains = $request->max_domains;
        if ($request->has('tier')) $key->tier = $request->tier;
        if ($request->has('features')) $key->features = $request->features;
        if ($request->has('metadata')) $key->metadata = $request->metadata;
        if ($request->has('expires_at')) $key->expires_at = $request->expires_at;
        if ($request->has('revoked')) {
            $key->revoked = $request->revoked;
            if ($request->revoked && $request->has('revocation_reason')) {
                $key->revocation_reason = $request->revocation_reason;
            }
        }

        $key->save();

        return response()->json($key);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $key = AccessKey::findOrFail($id);
        $key->delete();

        return response()->json(null, 204);
    }

    /**
     * Display a listing of the logs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs(Request $request)
    {
        $query = ValidationLog::query()->with('accessKey');

        // Apply filters
        if ($request->has('domain')) {
            $query->where('domain', 'like', '%' . $request->domain . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($logs);
    }

    /**
     * Display logs for a specific access key.
     *
     * @param  string  $accessKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function forKey(Request $request, $accessKey)
    {
        $query = ValidationLog::where('access_key', $accessKey);

        // Apply filters
        if ($request->has('domain')) {
            $query->where('domain', 'like', '%' . $request->domain . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($logs);
    }
}
