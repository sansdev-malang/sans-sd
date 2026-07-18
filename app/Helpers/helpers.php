<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get system setting value by key.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return new Setting();
        }

        return Setting::get($key, $default);
    }
}
