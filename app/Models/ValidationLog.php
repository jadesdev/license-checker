<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    use HasUuids;
    protected $fillable = [
        'access_key',
        'domain',
        'system_fingerprint',
        'request_id',
        'ip_address',
        'user_agent',
        'status',
        'message',
    ];

    /**
     * Get the access key that owns the log entry
     */
    public function accessKey()
    {
        return $this->belongsTo(AccessKey::class, 'access_key', 'key');
    }
}
