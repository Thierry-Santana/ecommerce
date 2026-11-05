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

$stmtUsuario = mysqli_prepare($conn, 'SELECT id, Nome, Email FROM Vendedor WHERE id = ? LIMIT 1');
if (!$stmtUsuario) {
    die('Erro ao preparar consulta do vendedor: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtUsuario, 'i', $id);
mysqli_stmt_execute($stmtUsuario);
$vendedor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUsuario));

if (!$vendedor) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($nome === '' || $email === '') {
        $mensagem = 'Nome e e-mail são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Informe um e-mail válido.';
    } else {
        $stmtExiste = mysqli_prepare($conn, 'SELECT id FROM Vendedor WHERE Email = ? AND id != ? LIMIT 1');
        if (!$stmtExiste) {
            $mensagem = 'Erro ao verificar e-mail do vendedor.';
        } else {
            mysqli_stmt_bind_param($stmtExiste, 'si', $email, $id);
            mysqli_stmt_execute($stmtExiste);
            $resultadoExiste = mysqli_stmt_get_result($stmtExiste);

            if (mysqli_num_rows($resultadoExiste) > 0) {
                $mensagem = 'Este e-mail já pertence a outro vendedor.';
            } else {
                if ($senha !== '') {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $sql = 'UPDATE Vendedor SET Nome = ?, Email = ?, Senha = ? WHERE id = ?';
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        $mensagem = 'Erro ao preparar atualização do vendedor.';
                    } else {
                        mysqli_stmt_bind_param($stmt, 'sssi', $nome, $email, $senhaHash, $id);
                    }
                } else {
                    $sql = 'UPDATE Vendedor SET Nome = ?, Email = ? WHERE id = ?';
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        $mensagem = 'Erro ao preparar atualização do vendedor.';
                    } else {
                        mysqli_stmt_bind_param($stmt, 'ssi', $nome, $email, $id);
                    }
                }

                if ($mensagem === '') {
                    if (!mysqli_stmt_execute($stmt)) {
                        $mensagem = 'Erro ao atualizar vendedor: ' . mysqli_stmt_error($stmt);
                    } else {
                        $mensagem = 'Vendedor atualizado com sucesso.';
                        $mensagemTipo = 'success';
                        $vendedor['Nome'] = $nome;
                        $vendedor['Email'] = $email;
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
    <title>Editar Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h2 class="mb-4">Editar vendedor</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" action="alterar.php?id=<?= (int) $id ?>">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($vendedor['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="email">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($vendedor['Email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="senha">Nova senha</label>
                            <input type="password" id="senha" name="senha" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                        <a href="listar.php" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
