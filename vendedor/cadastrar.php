<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';

$mensagem = '';
$mensagemTipo = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirmarSenha = trim($_POST['confirmarsenha'] ?? '');

    if ($nome === '' || $email === '' || $senha === '') {
        $mensagem = 'Nome, e-mail e senha são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Informe um e-mail válido.';
    } elseif ($senha !== $confirmarSenha) {
        $mensagem = 'As senhas informadas não conferem.';
    } else {
        $stmtExiste = mysqli_prepare($conn, 'SELECT id FROM Vendedor WHERE Email = ? LIMIT 1');
        if (!$stmtExiste) {
            $mensagem = 'Erro ao verificar e-mail do vendedor: ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmtExiste, 's', $email);
            mysqli_stmt_execute($stmtExiste);
            $resultadoExiste = mysqli_stmt_get_result($stmtExiste);

            if (mysqli_num_rows($resultadoExiste) > 0) {
                $mensagem = 'Este e-mail já está cadastrado para outro vendedor.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = 'INSERT INTO Vendedor (Nome, Email, Senha, Foto_perfil, Produtos_cadastrados, Chat) VALUES (?, ?, ?, ?, ?, ?)';
                $stmt = mysqli_prepare($conn, $sql);

                if (!$stmt) {
                    $mensagem = 'Erro ao preparar o cadastro do vendedor: ' . mysqli_error($conn);
                } else {
                    $fotoPerfil = '';
                    $produtosCadastrados = '';
                    $chat = '';

                    mysqli_stmt_bind_param($stmt, 'ssssss', $nome, $email, $senhaHash, $fotoPerfil, $produtosCadastrados, $chat);

                    if (!mysqli_stmt_execute($stmt)) {
                        $mensagem = 'Erro ao cadastrar vendedor: ' . mysqli_stmt_error($stmt);
                    } else {
                        $mensagem = 'Vendedor cadastrado com sucesso!';
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
    <title>Cadastrar Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h2 class="mb-4">Cadastrar vendedor</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="email">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="confirmarsenha">Confirmar senha</label>
                            <input type="password" id="confirmarsenha" name="confirmarsenha" class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar vendedor</button>
                        <a href="listar.php" class="btn btn-outline-secondary">Listar vendedores</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
