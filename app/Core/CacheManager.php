<?php
namespace App\Core;

use App\Core\Cache;
use App\Core\Config;
use App\Core\Request;
use App\Core\Logger;

class CacheManager
{
    private $cache;
    private $config;
    private $logger;
    private $cacheConfig;

    public function __construct()
    {
        $this->cache = new Cache();
        $this->config = Config::getInstance();
        $this->logger = Logger::getInstance('cache_manager');

        // بارگذاری تنظیمات کش از cache.php
        $configPath = __DIR__ . '/../../config/cache.php';
        if (file_exists($configPath)) {
            $this->cacheConfig = include $configPath;
            $this->logger->info("Loaded cache config: {$configPath}");
        } else {
            $this->cacheConfig = [
                'routes' => [
                    'product.index' => ['enabled' => true, 'ttl' => 600],
                    'home' => ['enabled' => true, 'ttl' => 600],
                    'cart.index' => ['enabled' => true, 'ttl' => 300],
                    'generate-csrf-token' => ['enabled' => false]
                ],
                'default_ttl' => $this->config->get('cache_ttl', 600)
            ];
            $this->logger->warning("No cache.php found, using default cache config");
        }
    }

    /**
     * بررسی و بازیابی خروجی کش‌شده برای یک درخواست
     */
    public function getCachedResponse(Request $request, callable $callback)
    {
        $routeName = $this->getRouteName($request);
        if (!$this->shouldCache($routeName, $request)) {
            $this->logger->info("Cache skipped for route: $routeName");
            return $callback();
        }

        $cacheKey = $this->generateCacheKey($routeName, $request);
        $cachedOutput = $this->cache->get($cacheKey);

        if ($cachedOutput !== false) {
            $this->logger->info("Serving cached response for route: $routeName");
            return $cachedOutput;
        }

        // اجرای callback برای تولید خروجی
        $output = $callback();

        // ذخیره خروجی در کش
        $ttl = $this->cacheConfig['routes'][$routeName]['ttl'] ?? $this->cacheConfig['default_ttl'];
        $this->cache->set($cacheKey, $output, $ttl);
        $this->logger->info("Cached response for route: $routeName with TTL: $ttl");

        return $output;
    }

    /**
     * پاک کردن کش برای یک مسیر یا کلید خاص
     */
    public function clearCache($routeName = null)
    {
        if ($routeName) {
            $this->cache->clear($routeName);
            $this->logger->info("Cleared cache for route: $routeName");
        } else {
            $this->cache->clear();
            $this->logger->info("Cleared all cache");
        }
    }

    /**
     * بررسی اینکه آیا باید کش شود یا خیر
     */
    private function shouldCache($routeName, Request $request)
    {
        // بررسی روش درخواست (فقط GET کش می‌شود)
        if ($request->getMethod() !== 'GET') {
            return false;
        }

        // بررسی تنظیمات کش برای مسیر
        if (!isset($this->cacheConfig['routes'][$routeName]) || !$this->cacheConfig['routes'][$routeName]['enabled']) {
            return false;
        }

        // بررسی وضعیت ورود کاربر (اختیاری: عدم کش برای کاربران واردشده)
        $session = new Session();
        if ($session->get('user_id')) {
            return false;
        }

        return true;
    }

    /**
     * تولید کلید کش بر اساس مسیر و پارامترهای درخواست
     */
    private function generateCacheKey($routeName, Request $request)
    {
        $params = $request->getQueryParams();
        unset($params['controller'], $params['action']); // حذف پارامترهای غیرضروری
        return $this->cache->generateCacheKey($routeName, $params);
    }

    /**
     * دریافت نام مسیر از درخواست
     */
    private function getRouteName(Request $request)
    {
        return Router::getRouteName($request);
    }
}
?>