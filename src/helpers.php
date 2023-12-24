<?php

use Illuminate\Support\Facades\Config;

if (!function_exists('getConfigNames')) {
    function getConfigNames(string $name)
    {
        return Config::get("controller-helpers.{$name}");
    }
}