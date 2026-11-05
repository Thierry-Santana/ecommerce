<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$mensagem = '';
$mensagemTipo = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $mensagem = 'O nome da categoria é obrigatório.';
    } else {
        $stmtExiste = mysqli_prepare($conn, 'SELECT id FROM Categoria WHERE Nome = ? LIMIT 1');
        if (!$stmtExiste) {
            $mensagem = 'Erro ao verificar categoria: ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmtExiste, 's', $nome);
            mysqli_stmt_execute($stmtExiste);
            $resultadoExiste = mysqli_stmt_get_result($stmtExiste);

            if (mysqli_num_rows($resultadoExiste) > 0) {
                $mensagem = 'Já existe uma categoria com este nome.';
            } else {
                $stmt = mysqli_prepare($conn, 'INSERT INTO Categoria (Nome) VALUES (?)');
                if (!$stmt) {
                    $mensagem = 'Erro ao preparar cadastro da categoria: ' . mysqli_error($conn);
                } else {
                    mysqli_stmt_bind_param($stmt, 's', $nome);

                    if (!mysqli_stmt_execute($stmt)) {
                        $mensagem = 'Erro ao cadastrar categoria: ' . mysqli_stmt_error($stmt);
                    } else {
                        $mensagem = 'Categoria cadastrada com sucesso!';
                        $mensagemTipo = 'success';
                    }
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h2 class="mb-4">Cadastrar categoria</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label" for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar categoria</button>
                        <a href="listar.php" class="btn btn-outline-secondary">Listar categorias</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
