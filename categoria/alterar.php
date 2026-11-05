<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$id = (int) ($_GET['id'] ?? 0);
$mensagem = '';
$mensagemTipo = 'danger';

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

$stmtCategoria = mysqli_prepare($conn, 'SELECT id, Nome FROM Categoria WHERE id = ? LIMIT 1');
if (!$stmtCategoria) {
    die('Erro ao preparar consulta da categoria: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtCategoria, 'i', $id);
mysqli_stmt_execute($stmtCategoria);
$categoria = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCategoria));

if (!$categoria) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $mensagem = 'O nome da categoria é obrigatório.';
    } else {
        $stmtExiste = mysqli_prepare($conn, 'SELECT id FROM Categoria WHERE Nome = ? AND id != ? LIMIT 1');
        if (!$stmtExiste) {
            $mensagem = 'Erro ao verificar categoria duplicada.';
        } else {
            mysqli_stmt_bind_param($stmtExiste, 'si', $nome, $id);
            mysqli_stmt_execute($stmtExiste);
            $resultadoExiste = mysqli_stmt_get_result($stmtExiste);

            if (mysqli_num_rows($resultadoExiste) > 0) {
                $mensagem = 'Já existe outra categoria com este nome.';
            } else {
                $stmt = mysqli_prepare($conn, 'UPDATE Categoria SET Nome = ? WHERE id = ?');
                if (!$stmt) {
                    $mensagem = 'Erro ao preparar atualização da categoria.';
                } else {
                    mysqli_stmt_bind_param($stmt, 'si', $nome, $id);

                    if (!mysqli_stmt_execute($stmt)) {
                        $mensagem = 'Erro ao atualizar categoria: ' . mysqli_stmt_error($stmt);
                    } else {
                        $mensagem = 'Categoria atualizada com sucesso.';
                        $mensagemTipo = 'success';
                        $categoria['Nome'] = $nome;
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
    <title>Editar Categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h2 class="mb-4">Editar categoria</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" action="alterar.php?id=<?= (int) $id ?>">
                    <div class="mb-3">
                        <label class="form-label" for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($categoria['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                        <a href="listar.php" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
