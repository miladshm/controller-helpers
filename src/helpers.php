<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('getConfigNames')) {
    function getConfigNames(string $name)
    {
        return Cache::get("controller-helpers.{$name}");
    }
}