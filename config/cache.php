<?php
return [
    'type' => 'file',
    'ttl' => 600,
    'file' => [
        'directory' => '/var/www/aliwebhosting.ir/storage/cache/'
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379
    ],
    'routes' => [
        'product.index' => [
            'enabled' => true,
            'ttl' => 600
        ],
        'home' => [
            'enabled' => true,
            'ttl' => 600
        ],
        'cart.index' => [
            'enabled' => true,
            'ttl' => 300
        ],
        'generate-csrf-token' => [
            'enabled' => false
        ]
    ],
    'default_ttl' => 600
];
?>