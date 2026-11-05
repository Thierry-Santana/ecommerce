<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_tipo']) && isset($_SESSION['Tipo'])) {
    $_SESSION['usuario_tipo'] = $_SESSION['Tipo'];
}

if (!isset($_SESSION['usuario_id']) && isset($_SESSION['id'])) {
    $_SESSION['usuario_id'] = $_SESSION['id'];
}

$tipoUsuario = $_SESSION['usuario_tipo'] ?? $_SESSION['Tipo'] ?? null;

if ($tipoUsuario !== 'admin') {
    $_SESSION['login_erro'] = 'Você precisa estar logado como administrador.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}