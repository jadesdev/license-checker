<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'access_key',
        'domain',
        'url',
        'system_fingerprint',
        'request_id',
        'ip_address',
        'user_agent',
        'status',
        'message',
        'meta',
        'auto_registered',
        'reset_domains',
        'registration_date',
        'main_domain',
        'metadata',
    ];

    protected $casts = [
        'meta' => 'json',
        'auto_registered' => 'boolean',
        'reset_domains' => 'boolean',
        'registration_date' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the access key that owns the log entry
     */
    public function accessKey()
    {
        return $this->belongsTo(AccessKey::class, 'access_key', 'key');
    }

    /**
     * Scope a query to only include active domain validations (not reset)
     */
    public function scopeActive($query)
    {
        return $query->where('reset_domains', false);
    }

    /**
     * Scope a query to only include recent validations
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }
}
