<?php
namespace App\Core;

use App\Core\Config;
use App\Core\Logger;

class Cache
{
    private $config;
    private $logger;
    private $cacheDir;
    private $cacheType;
    private $cacheTTL;
    private $redis;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->logger = Logger::getInstance('cache');

        // بارگذاری تنظیمات کش از cache.php
        $configPath = __DIR__ . '/../../config/cache.php';
        if (file_exists($configPath)) {
            $cacheConfig = include $configPath;
            $this->cacheType = $cacheConfig['type'] ?? 'file';
            $this->cacheTTL = $cacheConfig['ttl'] ?? $this->config->get('cache_ttl', 600);
            $this->cacheDir = $cacheConfig['file']['directory'] ?? __DIR__ . '/../../storage/cache/';
        } else {
            $this->cacheType = $this->config->get('cache_type', 'file');
            $this->cacheTTL = $this->config->get('cache_ttl', 600);
            $this->cacheDir = __DIR__ . '/../../storage/cache/';
        }

        // اطمینان از وجود پوشه کش برای نوع فایل
        if ($this->cacheType === 'file' && !is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
            $this->logger->info("Created cache directory: {$this->cacheDir}");
        }

        // تنظیمات Redis (در صورت استفاده در آینده)
        if ($this->cacheType === 'redis') {
            $this->redis = new \Redis();
            $host = $this->config->get('redis_host', '127.0.0.1');
            $port = $this->config->get('redis_port', 6379);
            try {
                $this->redis->connect($host, $port);
                $this->logger->info("Connected to Redis at {$host}:{$port}");
            } catch (\Exception $e) {
                $this->logger->error("Failed to connect to Redis: " . $e->getMessage());
                $this->cacheType = 'file'; // بازگشت به فایل در صورت خطا
            }
        }
    }

    /**
     * دریافت داده از کش
     */
    public function get($key)
    {
        $cacheKey = md5($key);
        if ($this->cacheType === 'file') {
            $cacheFile = $this->cacheDir . $cacheKey . '.cache';
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $this->cacheTTL) {
                $this->logger->info("Cache hit for key: $key");
                return file_get_contents($cacheFile);
            }
            $this->logger->info("Cache miss for key: $key");
            return false;
        } elseif ($this->cacheType === 'redis') {
            $data = $this->redis->get($cacheKey);
            if ($data !== false) {
                $this->logger->info("Cache hit for key: $key (Redis)");
                return $data;
            }
            $this->logger->info("Cache miss for key: $key (Redis)");
            return false;
        }
        return false;
    }

    /**
     * ذخیره داده در کش
     */
    public function set($key, $data, $ttl = null)
    {
        $cacheKey = md5($key);
        $ttl = $ttl ?? $this->cacheTTL;

        if ($this->cacheType === 'file') {
            $cacheFile = $this->cacheDir . $cacheKey . '.cache';
            file_put_contents($cacheFile, $data);
            $this->logger->info("Cached data for key: $key to $cacheFile");
        } elseif ($this->cacheType === 'redis') {
            $this->redis->setex($cacheKey, $ttl, $data);
            $this->logger->info("Cached data for key: $key (Redis)");
        }
    }

    /**
     * حذف یک کلید خاص از کش
     */
    public function delete($key)
    {
        $cacheKey = md5($key);
        if ($this->cacheType === 'file') {
            $cacheFile = $this->cacheDir . $cacheKey . '.cache';
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
                $this->logger->info("Deleted cache file for key: $key");
            }
        } elseif ($this->cacheType === 'redis') {
            $this->redis->del($cacheKey);
            $this->logger->info("Deleted cache for key: $key (Redis)");
        }
    }

    /**
     * پاک کردن همه کش‌ها یا کش‌های خاص
     */
    public function clear($prefix = null)
    {
        if ($this->cacheType === 'file') {
            $pattern = $prefix ? $this->cacheDir . $prefix . '*' : $this->cacheDir . '*.cache';
            foreach (glob($pattern) as $file) {
                unlink($file);
                $this->logger->info("Deleted cache file: $file");
            }
        } elseif ($this->cacheType === 'redis') {
            $keys = $prefix ? $this->redis->keys($prefix . '*') : $this->redis->keys('*');
            if (!empty($keys)) {
                $this->redis->del($keys);
                $this->logger->info("Cleared cache for prefix: $prefix (Redis)");
            }
        }
    }

    /**
     * تولید کلید کش بر اساس داده‌ها
     */
    public function generateCacheKey($prefix, $data = [])
    {
        return $prefix . '_' . md5(json_encode($data));
    }
}
?>