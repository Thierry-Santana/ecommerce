<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$idItem = (int) ($_GET['id'] ?? 0);
$usuarioId = (int) $_SESSION['usuario_id'];

if ($idItem > 0) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM Produtos_carrinho WHERE id = ? AND id_usuario = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $idItem, $usuarioId);
    mysqli_stmt_execute($stmt);
}

header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php');
exit;
