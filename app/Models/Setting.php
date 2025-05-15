<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::saved(function () {
            \Cache::forget('Settings');
        });
    }

    protected $fillable = [
        'title',
        'email',
        'admin_email',
        'support_email',
        'name',
        'description',
        'address',
        'phone',
        'logo',
        'favicon',
        'loader',
        'primary',
        'secondary',
        'last_cron',
        'custom_js',
        'custom_css',
        'currency',
        'currency_code',
        'currency_rate',

        'registration_active',
        'default_license_term',
        'max_domains_per_license',
        'license_expiration_alert',
    ];

    protected $casts = [
        'registration_active' => 'boolean',
    ];
}
