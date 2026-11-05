<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php?status=error&message=' . urlencode('Identificador do vendedor inválido.'));
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM Vendedor WHERE id = ?');
if (!$stmt) {
    header('Location: listar.php?status=error&message=' . urlencode('Erro ao preparar exclusão do vendedor: ' . mysqli_error($conn)));
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);

if (!mysqli_stmt_execute($stmt)) {
    $erro = mysqli_stmt_error($stmt);
    header('Location: listar.php?status=error&message=' . urlencode('Não foi possível excluir este vendedor. Motivo: ' . $erro));
    exit;
}

header('Location: listar.php?status=success');
exit;
