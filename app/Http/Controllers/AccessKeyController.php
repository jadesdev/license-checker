<?php

namespace App\Http\Controllers;

use App\Models\AccessKey;
use App\Models\ValidationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
            return $this->createSignedResponse([
                'valid' => false,
                'message' => 'Invalid request: ',
                'timestamp' => time(),
            ], 400);
        }

        // Extract validated data
        $metadata = $request->all();
        $domain = $request->domain;
        $mainDomain = $this->extractMainDomain($domain);
        $accessKey = $request->access_key;
        $fingerprint = $request->system_fingerprint;
        $requestId = $request->request_id;

        // Check request timestamp (prevent replay attacks)
        if (abs(time() - $request->timestamp) > 300) {
            return $this->createSignedResponse([
                'valid' => false,
                'message' => 'Request is invalid',
                'timestamp' => time(),
            ]);
        }

        //TODO:  add a check for the referr

        // Implement rate limiting per access key and IP
        $rateLimitKey = 'ratelimit:' . md5($accessKey . $request->ip());
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts > 60) { // 100 attempts per hour
            return $this->createSignedResponse([
                'valid' => false,
                'message' => 'Rate limit exceeded. Try again later.',
                'timestamp' => time(),
            ], 429);
        }

        Cache::put($rateLimitKey, $attempts + 1, 3600); // 1 hour

        // Look up the access key
        $license = AccessKey::where('key', $accessKey)->first();

        // Log this validation attempt regardless of outcome
        $log = new ValidationLog();
        $log->domain = $domain;
        $log->main_domain = $mainDomain;
        $log->access_key = $accessKey;
        $log->system_fingerprint = $fingerprint;
        $log->request_id = $requestId;
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        $log->url = $request->url;
        $log->metadata = ($metadata);

        // Begin validation process
        $message = '';
        $expiresAt = null;

        if (!$license) {
            $message = 'Access key not found';
            $log->status = 'invalid';
            $log->message = $message;
            $log->save();

            return $this->createSignedResponse([
                'valid' => false,
                'message' => $message,
                'timestamp' => time(),
            ]);
        }

        // Check if key is revoked
        if ($license->revoked) {
            $message = 'Access key has been revoked';
            $log->status = 'revoked';
            $log->message = $message;
            $log->save();

            return $this->createSignedResponse([
                'valid' => false,
                'message' => $message,
                'timestamp' => time(),
            ]);
        }

        // Check if key is expired
        if ($license->expires_at && Carbon::parse($license->expires_at)->isPast()) {
            $message = 'Access key has expired';
            $log->status = 'expired';
            $log->message = $message;
            $log->save();

            return $this->createSignedResponse([
                'valid' => false,
                'message' => $message,
                'expires_at' => $license->expires_at,
                'timestamp' => time(),
            ]);
        }

        // // Check if domain is allowed
        if (!$this->isDomainAllowed($license, $domain) && !$license->allow_auto_registration) {
            $message = 'Access key is not valid for this domain';
            $log->status = 'domain_mismatch';
            $log->message = $message;
            $log->save();

            return $this->createSignedResponse([
                'valid' => false,
                'message' => $message,
                'timestamp' => time(),
            ]);
        }

        // Check for installation limits
        if ($license->max_domains > 0) {
            // Check if this domain is already allowed
            if (!$this->isDomainAllowed($license, $domain)) {
                // Domain not explicitly allowed, but we might auto-register it
                if (!$license->hasReachedDomainLimit() && $license->allow_auto_registration) {
                    // We're under the limit, so automatically add this domain
                    $wasAdded = $this->handleNewDomain($license, $domain);

                    if ($wasAdded) {
                        $message = 'Domain automatically registered with this license';
                        $log->status = 'domain_registered';
                        $log->message = $message;
                        $log->auto_registered = true;
                        $log->registration_date = now();
                    }
                } else {
                    $message = 'Maximum number of installations reached';
                    $log->status = 'limit_reached';
                    $log->message = $message;
                    $log->save();

                    return $this->createSignedResponse([
                        'valid' => false,
                        'message' => $message,
                        // 'max_domains' => $license->max_domains,
                        // 'current_domains' => $license->active_domains,
                        // 'current_count' => count($license->active_domains),
                        'timestamp' => time(),
                    ]);
                }
            }
        }

        // If we've made it here, the key is valid
        $message = 'Access key is valid';
        $expiresAt = $license->expires_at;

        $log->status = 'valid';
        $log->message = $message;
        $log->save();

        // Update the last used timestamp on the license
        $license->last_used_at = Carbon::now();
        $license->save();

        return $this->createSignedResponse([
            'valid' => true,
            'message' => $message,
            'expires_at' => $expiresAt,
            'tier' => $license->tier,
            // 'features' => $license->features,
            // 'metadata' => $license->metadata,
            'next_check_interval' => rand(6 * 3600, 24 * 3600), // 6-24 hours
            'timestamp' => time(),
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

        return $license->isDomainAllowed($domain);
    }

    /**
     * Get unique domains that have been active in the last 30 days
     *
     * @param string $accessKey
     * @return array
     */
    protected function getActiveDomains(string $accessKey)
    {
        $license = AccessKey::where('key', $accessKey)->first();
        return $license ? $license->active_domains : [];
    }

    /**
     * Create a response with a signed verification header
     *
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createSignedResponse(array $data, int $status = 200)
    {
        // Sort data to ensure consistent signature
        $signatureData = $data;
        ksort($signatureData);

        // Generate signature
        $dataString = json_encode($signatureData);
        $accessKey = $data['access_key'] ?? request('access_key');
        $secret = hash('sha256', $accessKey . '_verification_key');
        $signature = hash_hmac('sha256', $dataString, $secret);

        // Return JSON response with signature header
        return response()->json($data, $status)
            ->header('signature', $signature);
    }

    /**
     * Handle new domain registration for an access key
     * Automatically adds domain to allowed list if under max_domains limit
     *
     * @param AccessKey $license
     * @param string $domain
     * @return bool Whether the domain was successfully added
     */
    protected function handleNewDomain(AccessKey $license, string $domain)
    {
        if (!$license->allow_auto_registration) {
            return false;
        }

        // Check if we're within the domain limit
        if (!$license->hasReachedDomainLimit()) {
            // Add the domain
            $added = $license->addAllowedDomain($domain);

            if ($added) {
                // Create a special log entry for this auto-registration
                ValidationLog::create([
                    'access_key' => $license->key,
                    'domain' => $domain,
                    'system_fingerprint' => request('system_fingerprint'),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'domain_registered',
                    'message' => 'Domain automatically registered with this license',
                    'auto_registered' => true,
                    'registration_date' => now(),
                ]);

                // Log this auto-addition
                \Log::info("Domain {$domain} automatically added to license {$license->key}");
            }

            return $added;
        }

        return false;
    }
    /**
     * Get statistics about key usage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function keyStats(Request $request)
    {
        // Require authentication for this endpoint
        if (!\Auth::user()->can('view access key stats')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $key = $request->input('key');
        $license = AccessKey::where('key', $key)->first();

        if (!$license) {
            return response()->json(['error' => 'Key not found'], 404);
        }

        // Get usage statistics
        $stats = [
            'total_validations' => ValidationLog::where('access_key', $key)->count(),
            'valid_validations' => ValidationLog::where('access_key', $key)->where('status', 'valid')->count(),
            'active_domains' => $this->getActiveDomains($key),
            'recent_validations' => ValidationLog::where('access_key', $key)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get(),
            'validation_counts_by_day' => ValidationLog::where('access_key', $key)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get()
                ->pluck('count', 'date')
        ];

        return response()->json($stats);
    }

    /**
     * Reset domain tracking for a key
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetDomains(Request $request)
    {
        // Require authentication for this endpoint
        if (!\Auth::user()->can('manage access keys')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $key = $request->input('key');

        // Mark all validations as inactive for domain counting
        ValidationLog::where('access_key', $key)
            ->update(['reset_domains' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Domain tracking has been reset for this key'
        ]);
    }


    /**
     * Check if we're in grace period for this license
     * Using the configurable grace period from the license
     *
     * @param AccessKey $license
     * @param ValidationLog $lastSuccessLog
     * @return bool
     */
    protected function isInGracePeriod(AccessKey $license, $lastSuccessLog)
    {
        if (!$lastSuccessLog) {
            return false;
        }

        $graceHours = $license->grace_period_hours ?? 72;
        $timeElapsed = Carbon::now()->diffInHours($lastSuccessLog->created_at);

        return $timeElapsed < $graceHours;
    }

    /**
     * Extract the main domain from a full domain string
     * 
     * @param string $domain
     * @return string
     */
    protected function extractMainDomain(string $host): string
    {
        // Normalize host (strip protocol, trailing slashes)
        $host = strtolower(trim(preg_replace('/^https?:\/\//', '', $host)));
        $host = preg_replace('/\/.*$/', '', $host); // Remove path

        // Common public suffixes
        $multiPartTLDs = [
            'co.uk',
            'org.uk',
            'ac.uk',
            'gov.uk',
            'com.ng',
            'gov.ng',
            'edu.ng',
            'org.ng',
            'net.ng',
            'co.za',
            'org.za',
        ];

        foreach ($multiPartTLDs as $tld) {
            if (str_ends_with($host, '.' . $tld)) {
                $parts = explode('.', $host);
                $domainParts = array_slice($parts, - ($this->countDots($tld) + 2));
                return implode('.', $domainParts);
            }
        }

        // Default fallback for single-part TLDs like .com, .net
        $parts = explode('.', $host);
        $count = count($parts);

        if ($count >= 2) {
            return $parts[$count - 2] . '.' . $parts[$count - 1];
        }

        // Fallback (e.g. localhost or invalid domain)
        return $host;
    }

    protected function countDots(string $str): int
    {
        return substr_count($str, '.');
    }
}
