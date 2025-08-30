<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Installer;
use App\Core\Logger;

// بررسی اینکه اسکریپت از خط فرمان اجرا شده است
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

$installer = new Installer();
$logger = Logger::getInstance('install_script');

try {
    if ($installer->isInstalled()) {
        die("سیستم قبلاً نصب شده است. برای نصب مجدد، فایل storage/installed.lock را حذف کنید.\n");
    }

    // اضافه کردن ورودی تعاملی برای تنظیمات (اختیاری)
    echo "لطفاً نام دیتابیس را وارد کنید (پیش‌فرض: hosting_cms): ";
    $dbName = trim(fgets(STDIN)) ?: 'hosting_cms';
    // می‌توانید برای سایر تنظیمات مانند DB_HOST، DB_USER و غیره هم ورودی بگیرید

    $installer->run();
    echo "نصب با موفقیت انجام شد! به صفحه اصلی بروید: " . Config::getInstance()->get('app_url') . "\n";
} catch (\Exception $e) {
    $logger->error("Installation failed: " . $e->getMessage());
    echo "خطا در نصب: " . $e->getMessage() . "\n";
}
?>
