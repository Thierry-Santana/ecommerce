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

$itemId = (int) ($_POST['id'] ?? 0);
$quantidade = max(1, (int) ($_POST['quantidade'] ?? 1));
$usuarioId = (int) $_SESSION['usuario_id'];

if ($itemId > 0) {
    $stmtEstoque = mysqli_prepare($conn, 'SELECT p.Estoque FROM Produtos_carrinho AS c INNER JOIN Produto AS p ON p.id = c.id_produto WHERE c.id = ? AND c.id_usuario = ? LIMIT 1');
    if ($stmtEstoque) {
        mysqli_stmt_bind_param($stmtEstoque, 'ii', $itemId, $usuarioId);
        mysqli_stmt_execute($stmtEstoque);
        $produtoCarrinho = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtEstoque));

        if ($produtoCarrinho) {
            $quantidade = min($quantidade, max(0, (int) $produtoCarrinho['Estoque']));
            if ($quantidade > 0) {
                $stmt = mysqli_prepare($conn, 'UPDATE Produtos_carrinho SET Quantidade = ? WHERE id = ? AND id_usuario = ?');
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'iii', $quantidade, $itemId, $usuarioId);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }
}

header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php');
exit;
