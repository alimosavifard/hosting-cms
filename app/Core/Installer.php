<?php
namespace App\Core;

use PDO;
use App\Core\Database;
use App\Core\Theme;

class Installer
{
    private $pdo;
    private $logger;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->logger = Logger::getInstance('installer');
    }

    public function run()
    {
        try {
            // اجرای migrationها
            $this->runMigrations();

            // اجرای seederها
            $this->runSeeders();

            // کپی فایل‌های استاتیک قالب پیش‌فرض (frontend)
            $theme = new Theme();
            $theme->copyAssets();

            // کپی فایل‌های استاتیک ادمین (backend)
            $adminTheme = new Theme(true);
            $adminTheme->copyAssets();

            // علامت‌گذاری نصب به‌عنوان کامل‌شده
            $this->markAsInstalled();

            $this->logger->info("Installation completed successfully");
        } catch (\Exception $e) {
            $this->logger->error("Installation failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function runMigrations()
    {
        $migrationPath = __DIR__ . '/../../database/migrations/';
        $files = glob($migrationPath . '*.php');

        if (empty($files)) {
            $this->logger->error("No migration files found in $migrationPath");
            throw new \Exception("هیچ فایل migration یافت نشد");
        }

        foreach ($files as $file) {
            $migration = require $file;
            $migrationClass = basename($file, '.php');
            $className = 'Migration_' . $migrationClass;

            if (class_exists($className)) {
                $migrationInstance = new $className($this->pdo);
                $migrationInstance->up();
                $this->logger->info("Applied migration: $migrationClass");
            } else {
                $this->logger->error("Migration class $className not found");
            }
        }
    }

    private function runSeeders()
    {
        $seederPath = __DIR__ . '/../../database/seeders/';
        $files = glob($seederPath . '*.php');

        if (empty($files)) {
            $this->logger->warning("No seeder files found in $seederPath");
        }

        foreach ($files as $file) {
            $seeder = require $file;
            $seederClass = basename($file, '.php');
            $className = 'Seeder_' . $seederClass;

            if (class_exists($className)) {
                $seederInstance = new $className($this->pdo);
                $seederInstance->run();
                $this->logger->info("Applied seeder: $seederClass");
            }
        }
    }

    private function markAsInstalled()
    {
        file_put_contents(__DIR__ . '/../../storage/installed.lock', date('Y-m-d H:i:s'));
    }

    public function isInstalled(): bool
    {
        return file_exists(__DIR__ . '/../../storage/installed.lock');
    }
}
?>