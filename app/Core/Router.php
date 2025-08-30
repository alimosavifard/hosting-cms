<?php
namespace App\Core;

use App\Core\Request;
use App\Core\Response;

class Router
{
    private static $routes = [];
    private $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        $cacheFile = __DIR__ . '/../../storage/cache/routes.json';
        if (file_exists($cacheFile) && !$this->config->get('debug')) {
            self::$routes = json_decode(file_get_contents($cacheFile), true);
            return;
        }

        // بارگذاری روت‌ها از فایل پیکربندی
        $routes = require __DIR__ . '/../../config/routes.php';
        foreach ($routes as $routeName => $route) {
            self::$routes[$route['path']] = [
                'name' => $routeName,
                'controller' => $route['controller'],
                'action' => $route['action'],
                'params' => $route['params'] ?? []
            ];
        }

        // ذخیره در کش
        file_put_contents($cacheFile, json_encode(self::$routes));
    }

    public static function getRoute($routeName, $params = []): string
    {
        $config = Config::getInstance();
        foreach (self::$routes as $path => $route) {
            if ($route['name'] === $routeName) {
                $url = $config->get('app_url') . $path;
                if (!empty($params)) {
                    // جایگزینی پارامترهای پویا در مسیر
                    $dynamicPath = $path;
                    foreach ($route['params'] ?? [] as $paramName) {
                        if (isset($params[$paramName])) {
                            $dynamicPath = preg_replace('/\(\.\+\)/', $params[$paramName], $dynamicPath, 1);
                            unset($params[$paramName]);
                        }
                    }
                    $url = $config->get('app_url') . $dynamicPath;
                    if (!empty($params)) {
                        $url .= '?' . http_build_query($params);
                    }
                }
                return $url;
            }
        }
        throw new \Exception("Route name '$routeName' not found");
    }

    public function dispatch(Request $request)
    {
        $path = $request->getServer('REQUEST_URI', '/');
        $path = parse_url($path, PHP_URL_PATH); // حذف query string
        $path = '/' . trim($path, '/');

        foreach (self::$routes as $routePath => $route) {
            $pattern = '@^' . preg_quote($routePath, '@') . '$@';
            $pattern = preg_replace('@\\\(d\+)@', '(\d+)', $pattern);
            if (preg_match($pattern, $path, $matches)) {
                $controller = $route['controller'];
                $action = $route['action'];

                // افزودن پارامترهای پویا
                if (isset($route['params'])) {
                    foreach ($route['params'] as $index => $paramName) {
                        $request->get[$paramName] = $matches[$index + 1];
                    }
                }

                $controllerClass = 'App\\Controllers\\' . ucfirst($controller) . 'Controller';
                if (!class_exists($controllerClass)) {
                    throw new \Exception("Controller $controllerClass not found");
                }

                $controllerInstance = new $controllerClass();
                if (!method_exists($controllerInstance, $action)) {
                    throw new \Exception("Action $action not found in $controllerClass");
                }

                return $controllerInstance->$action($request);
            }
        }

        throw new \Exception("Route not found for path: $path");
    }

    /**
     * دریافت نام مسیر بر اساس درخواست
     */
    public static function getRouteName(Request $request): string
    {
        $path = $request->getServer('REQUEST_URI', '/');
        $path = parse_url($path, PHP_URL_PATH);
        $path = '/' . trim($path, '/');
        foreach (self::$routes as $routePath => $route) {
            $pattern = '@^' . preg_quote($routePath, '@') . '$@';
            $pattern = preg_replace('@\\\(d\+)@', '(\d+)', $pattern);
            if (preg_match($pattern, $path)) {
                return $route['name'];
            }
        }
        return 'unknown';
    }
}
?>