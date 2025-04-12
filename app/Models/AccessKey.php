<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked' => 'boolean',
        'features' => 'json',
        'metadata' => 'json',
    ];
    /**
     * Get the validation logs for the access key
     */
    public function validationLogs()
    {
        return $this->hasMany(ValidationLog::class, 'access_key', 'key');
    }

    /**
     * Generate a new access key
     */
    public static function generateKey()
    {
        return bin2hex(random_bytes(24)); // use uuid
    }
}
