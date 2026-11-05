<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$produtoId = (int) ($_GET['id'] ?? 0);

if ($produtoId > 0) {
    $stmt = mysqli_prepare($conn, 'INSERT IGNORE INTO Produto_desejo (id_usuario, id_produto) VALUES (?, ?)');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $usuarioId, $produtoId);
        mysqli_stmt_execute($stmt);
    }
}

header('Location: ' . BASE_URL . 'telas/compra/tela_compra.php?id=' . $produtoId);
exit;