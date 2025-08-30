<?php
namespace App\Core;

use Dotenv\Dotenv;

class Config
{
    private static $instance = null;
    private $settings = [];

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->settings = [
            'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
            'db_name' => $_ENV['DB_NAME'] ?? 'hosting_cms',
            'db_user' => $_ENV['DB_USER'] ?? 'root',
            'db_pass' => $_ENV['DB_PASS'] ?? '',
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost',
            'debug' => $_ENV['APP_DEBUG'] ?? true,
            'theme' => $_ENV['THEME'] ?? 'default',
            'cache_type' => $_ENV['CACHE_TYPE'] ?? 'file',
            'cache_ttl' => $_ENV['CACHE_TTL'] ?? 600,
            'redis_host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'redis_port' => $_ENV['REDIS_PORT'] ?? 6379
        ];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }
}
?>