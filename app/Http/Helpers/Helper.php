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

if (! function_exists('static_asset')) {
    function static_asset($path, $secure = null)
    {
        if (php_sapi_name() == 'cli-server') {
            return app('url')->asset('assets/' . $path, $secure);
        }

        return app('url')->asset('public/assets/' . $path, $secure);
    }
}

// return file uploaded via uploader
if (! function_exists('my_asset')) {
    function my_asset($path, $secure = null)
    {
        if (php_sapi_name() == 'cli-server') {
            return app('url')->asset('uploads/' . $path, $secure);
        }

        return app('url')->asset('public/uploads/' . $path, $secure);
    }
}
