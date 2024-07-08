<!-- for admin panel -->

<?php
// index.php

$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/api/admins':
        require __DIR__ . '/api/admins.php';
        break;
    case '/api/areas':
        require __DIR__ . '/api/areas.php';
        break;
    case '/api/categories':
        require __DIR__ . '/api/categories.php';
        break;
    case '/api/inquiries':
        require __DIR__ . '/api/inquiries.php';
        break;
    case '/api/orders':
        require __DIR__ . '/api/orders.php';
        break;
    case '/api/products':
        require __DIR__ . '/api/products.php';
        break;
    default:
        http_response_code(404);
        echo 'Not Found';
        break;
}
?>
