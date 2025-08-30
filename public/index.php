<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers.php';

use App\Core\Request;
use App\Core\Router;

try {
    $request = new Request();
    $router = new Router();
    echo $router->dispatch($request);
} catch (\Exception $e) {
    echo "خطا: " . htmlspecialchars($e->getMessage());
}