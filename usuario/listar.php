<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$stmt = mysqli_prepare($conn, 'SELECT id, Nome, Email, Tipo FROM Usuario ORDER BY id DESC');
if (!$stmt) {
    die('Erro ao consultar usuários: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Usuários</h2>
            <a href="cadastrar.php" class="btn btn-primary">Novo usuário</a>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= (int) $usuario['id'] ?></td>
                                <td><?= htmlspecialchars($usuario['Nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['Email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['Tipo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="alterar.php?id=<?= (int) $usuario['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="deletar.php?id=<?= (int) $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este usuário?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
