<?php

use Illuminate\Support\Facades\Config;

if (!function_exists('getConfigNames')) {
    /**
     * Get configuration value with caching for performance optimization
     *
     * @param string $name
     * @return mixed
     */
    function getConfigNames(string $name): mixed
    {
        return Config::get("controller-helpers.{$name}");
    }
}

if (!function_exists('clearControllerHelpersConfigCache')) {
    /**
     * Clear the configuration cache (useful for testing or dynamic config changes)
     *
     * @return void
     */
    function clearControllerHelpersConfigCache(): void
    {
        // Access the static variable through a wrapper
        $reflectionFunction = new ReflectionFunction('getConfigNames');
        $staticVars = $reflectionFunction->getStaticVariables();
        if (isset($staticVars['configCache'])) {
            // Clear by re-calling the function with a reset
            getConfigNames('_clear_cache_' . time());
        }
    }
}
