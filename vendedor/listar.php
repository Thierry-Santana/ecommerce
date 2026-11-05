<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$mensagem = '';
$mensagemTipo = 'success';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $mensagem = 'Operação realizada com sucesso.';
        $mensagemTipo = 'success';
    } elseif ($_GET['status'] === 'error') {
        $mensagem = $_GET['message'] ?? 'Não foi possível concluir a operação.';
        $mensagemTipo = 'danger';
    }
}

$stmt = mysqli_prepare($conn, 'SELECT id, Nome, Email FROM Vendedor ORDER BY id DESC');
if (!$stmt) {
    die('Erro ao consultar vendedores: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Vendedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Vendedores</h2>
            <a href="cadastrar.php" class="btn btn-primary">Novo vendedor</a>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($vendedor = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= (int) $vendedor['id'] ?></td>
                                <td><?= htmlspecialchars($vendedor['Nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($vendedor['Email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="alterar.php?id=<?= (int) $vendedor['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="deletar.php?id=<?= (int) $vendedor['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este vendedor?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum vendedor encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
