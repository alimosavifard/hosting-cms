<?php

use App\Core\Router;

if (!function_exists('route')) {
    /**
     * Generate a URL for a named route.
     *
     * @param string $routeName
     * @param array $params
     * @return string
     */
    function route($routeName, $params = []): string
    {
        return Router::getRoute($routeName, $params);
    }
}
