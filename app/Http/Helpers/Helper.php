<?php

use App\Models\Setting;

if (! function_exists('get_setting')) {
    function get_setting($key = null)
    {
        $settings = Cache::get('Settings');

        if (! $settings) {
            $settings = Setting::first();
            Cache::put('Settings', $settings, 30000);
        }

        if ($key) {
            return @$settings->$key;
        }

        return $settings;
    }
}
