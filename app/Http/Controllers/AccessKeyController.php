<?php

namespace App\Http\Controllers;

use App\Models\AccessKey;
use App\Models\ValidationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccessKeyController extends Controller
{
    /**
     * Validate an access key
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'url' => 'required|url',
            'access_key' => 'required|string|max:64',
            'system_fingerprint' => 'nullable|string|max:255',
            'method' => 'required|string|in:GET,POST,PUT,DELETE,PATCH,get,post,put.delete,patch',
            'timestamp' => 'required|integer',
            'request_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid request: ' . $validator->errors()->first(),
            ], 400);
        }
        // Extract validated data
        $domain = $request->domain;
        $accessKey = $request->access_key;
        $fingerprint = $request->system_fingerprint;
        $requestId = $request->request_id;

        // Look up the access key
        $license = AccessKey::where('key', $accessKey)->first();

        // Log this validation attempt regardless of outcome
        $log = new ValidationLog();
        $log->domain = $domain;
        $log->access_key = $accessKey;
        $log->system_fingerprint = $fingerprint;
        $log->request_id = $requestId;
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();

        // Begin validation process
        $isValid = false;
        $message = '';
        $expiresAt = null;

        if (!$license) {
            $message = 'Access key not found';
            $log->status = 'invalid';
            $log->message = $message;
            $log->save();

            return response()->json([
                'valid' => false,
                'message' => $message,
            ]);
        }

        // Check if key is revoked
        if ($license->revoked) {
            $message = 'Access key has been revoked';
            $log->status = 'revoked';
            $log->message = $message;
            $log->save();

            return response()->json([
                'valid' => false,
                'message' => $message,
            ]);
        }

        // Check if key is expired
        if ($license->expires_at && Carbon::parse($license->expires_at)->isPast()) {
            $message = 'Access key has expired';
            $log->status = 'expired';
            $log->message = $message;
            $log->save();

            return response()->json([
                'valid' => false,
                'message' => $message,
                'expires_at' => $license->expires_at,
            ]);
        }

        // Check if domain is allowed
        if (!$this->isDomainAllowed($license, $domain)) {
            $message = 'Access key is not valid for this domain';
            $log->status = 'domain_mismatch';
            $log->message = $message;
            $log->save();

            return response()->json([
                'valid' => false,
                'message' => $message,
            ]);
        }

        // Check for installation limits
        if ($license->max_domains > 0) {
            $activeInstallations = ValidationLog::where('access_key', $accessKey)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->distinct('domain')
                ->count('domain');

            if ($activeInstallations >= $license->max_domains && !in_array($domain, $this->getActiveDomains($accessKey))) {
                $message = 'Maximum number of installations reached';
                $log->status = 'limit_reached';
                $log->message = $message;
                $log->save();

                return response()->json([
                    'valid' => false,
                    'message' => $message,
                    'max_domains' => $license->max_domains,
                    'current_count' => $activeInstallations,
                ]);
            }
        }

        // If we've made it here, the key is valid
        $isValid = true;
        $message = 'Access key is valid';
        $expiresAt = $license->expires_at;

        $log->status = 'valid';
        $log->message = $message;
        $log->save();

        // Update the last used timestamp on the license
        $license->last_used_at = Carbon::now();
        $license->save();

        return response()->json([
            'valid' => true,
            'message' => $message,
            'expires_at' => $expiresAt,
            'tier' => $license->tier,
            'features' => $license->features,
            'metadata' => $license->metadata,
        ]);
    }

    /**
     * Check if the domain is allowed for this license
     *
     * @param AccessKey $license
     * @param string $domain
     * @return bool
     */
    protected function isDomainAllowed(AccessKey $license, string $domain)
    {
        // If no domains are specified, the key is valid for any domain
        if (empty($license->allowed_domains)) {
            return true;
        }

        $allowedDomains = json_decode($license->allowed_domains, true);

        if (!is_array($allowedDomains)) {
            return true; // Fallback in case of data corruption
        }

        foreach ($allowedDomains as $allowedDomain) {
            // Handle wildcards
            if (strpos($allowedDomain, '*') !== false) {
                $pattern = str_replace('\*', '.*', preg_quote($allowedDomain, '/'));
                if (preg_match('/^' . $pattern . '$/i', $domain)) {
                    return true;
                }
            } else {
                if (strtolower($allowedDomain) === strtolower($domain)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get unique domains that have been active in the last 30 days
     *
     * @param string $accessKey
     * @return array
     */
    protected function getActiveDomains(string $accessKey)
    {
        return ValidationLog::where('access_key', $accessKey)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('status', 'valid')
            ->distinct()
            ->pluck('domain')
            ->toArray();
    }
}
