<?php

$https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
);

$protocolo = $https ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

if ($host === 'localhost') {
    $basePath = '/ecommerce/';
} else {
    $basePath = '/';
}

define('BASE_URL', $protocolo . $host . $basePath);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}
