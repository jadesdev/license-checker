<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AccessKey extends Model
{
    use HasUuids;

    protected $fillable = [
        'key',
        'owner_name',
        'owner_email',
        'allowed_domains',
        'max_domains',
        'tier',
        'features',
        'metadata',
        'expires_at',
        'revoked',
        'revocation_reason',
        'allow_auto_registration',
        'allow_localhost',
        'grace_period_hours',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked' => 'boolean',
        'features' => 'json',
        'metadata' => 'json',
        'allowed_domains' => 'json',
        'allow_auto_registration' => 'boolean',
        'allow_localhost' => 'boolean',
    ];

    /**
     * Get the validation logs for the access key
     */
    public function validationLogs()
    {
        return $this->hasMany(ValidationLog::class, 'access_key', 'key');
    }

    /**
     * Get active domains for this access key
     */
    public function getActiveDomainsAttribute()
    {
        return $this->validationLogs()
            ->active()
            ->recent()
            ->where('status', 'valid')
            ->distinct('domain')
            ->pluck('domain')
            ->toArray();
    }

    /**
     * Check if a domain is allowed for this license
     */
    public function isDomainAllowed($domain)
    {
        // If no domains are specified, the key is valid for any domain
        if (empty($this->allowed_domains)) {
            return true;
        }

        $allowedDomains = $this->allowed_domains;

        if (!is_array($allowedDomains)) {
            return true; // Fallback in case of data corruption
        }

        // Handle localhost and development environments
        if (in_array($domain, ['localhost', '127.0.0.1', '::1']) && $this->allow_localhost) {
            return true;
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
     * Add a domain to the allowed domains list
     */
    public function addAllowedDomain($domain)
    {
        $allowedDomains = $this->allowed_domains ?: [];

        if (!in_array($domain, $allowedDomains)) {
            $allowedDomains[] = $domain;
            $this->allowed_domains = $allowedDomains;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Remove a domain from the allowed domains list
     */
    public function removeAllowedDomain($domain)
    {
        $allowedDomains = $this->allowed_domains ?: [];

        if (($key = array_search($domain, $allowedDomains)) !== false) {
            unset($allowedDomains[$key]);
            $this->allowed_domains = array_values($allowedDomains); // Re-index array
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Generate a new access key
     */
    public static function generateKey()
    {
        return (string) \Str::uuid();
    }

    /**
     * Check if this license has reached its domain limit
     */
    public function hasReachedDomainLimit()
    {
        return count($this->active_domains) >= $this->max_domains;
    }

    /**
     * Check if the license is expired
     */
    public function isExpired()
    {
        return $this->expires_at && Carbon::parse($this->expires_at)->isPast();
    }

    /**
     * Get the days until expiration
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, Carbon::now()->diffInDays($this->expires_at, false));
    }
}
