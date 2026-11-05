<?php

date_default_timezone_set('America/Sao_Paulo');

// Configuração para ambiente online
/*$host = 'sql306.infinityfree.com';
$user = 'if0_42679030';
$password = 'gEesMiVkUt5';
$db = 'if0_42679030_ecommerce';

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4"); */

// Configuração para ambiente local
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'ecommerce';

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");