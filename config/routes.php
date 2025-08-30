<?php
return [
    'home' => [
        'path' => '/',
        'controller' => 'product',
        'action' => 'index'
    ],
    'product.index' => [
        'path' => '/products',
        'controller' => 'product',
        'action' => 'index'
    ],
    'cart.index' => [
        'path' => '/cart',
        'controller' => 'cart',
        'action' => 'index'
    ],
    'cart.add' => [
        'path' => '/cart/add',
        'controller' => 'cart',
        'action' => 'add'
    ],
    'cart.checkout' => [
        'path' => '/cart/checkout',
        'controller' => 'cart',
        'action' => 'checkout'
    ],
    'cart.success' => [
        'path' => '/cart/success',
        'controller' => 'cart',
        'action' => 'success'
    ],
    'user.login' => [
        'path' => '/login',
        'controller' => 'user',
        'action' => 'login'
    ],
    'user.register' => [
        'path' => '/register',
        'controller' => 'user',
        'action' => 'register'
    ],
    'user.logout' => [
        'path' => '/logout',
        'controller' => 'user',
        'action' => 'logout'
    ],
    'user.profile' => [
        'path' => '/profile',
        'controller' => 'user',
        'action' => 'profile'
    ],
    'admin.index' => [
        'path' => '/admin',
        'controller' => 'admin',
        'action' => 'index'
    ],
    'admin.addProduct' => [
        'path' => '/admin/products/add',
        'controller' => 'admin',
        'action' => 'addProduct'
    ],
    'admin.editProduct' => [
        'path' => '/admin/products/edit/(\d+)',
        'controller' => 'admin',
        'action' => 'editProduct',
        'params' => ['product_id']
    ],
    'admin.deleteProduct' => [
        'path' => '/admin/products/delete',
        'controller' => 'admin',
        'action' => 'deleteProduct'
    ],
    'generate-csrf-token' => [
        'path' => '/generate-csrf-token',
        'controller' => 'session',
        'action' => 'generateCsrfToken'
    ]
];