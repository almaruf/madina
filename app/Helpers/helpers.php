<?php

/**
 * Helper functions for the application
 */

if (!function_exists('vite_css_path')) {
    /**
     * Get the CSS path from Vite
     *
     * @return string|null
     */
    function vite_css_path()
    {
        // Since we're not using Vite in this project (using Tailwind CDN instead),
        // return null to indicate no Vite CSS to load
        return null;
    }
}

if (!function_exists('vite_js_path')) {
    /**
     * Get the JS path from Vite
     *
     * @return string|null
     */
    function vite_js_path()
    {
        // Since we're not using Vite in this project (using Tailwind CDN instead),
        // return null to indicate no Vite JS to load
        return null;
    }
}
