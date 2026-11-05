<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$resultado = mysqli_query($conn, 'SELECT * FROM Produto ORDER BY id DESC');
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Produtos</h2>
            <a href="cadastrar.php" class="btn btn-primary">Novo produto</a>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($produto = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= (int) $produto['id'] ?></td>
                                <td><?= htmlspecialchars($produto['Nome']) ?></td>
                                <td>R$ <?= number_format((float) $produto['Preco'], 2, ',', '.') ?></td>
                                <td><?= (int) $produto['Estoque'] ?></td>
                                <td>
                                    <a href="alterar.php?id=<?= (int) $produto['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="deletar.php?id=<?= (int) $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este produto?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum produto encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
