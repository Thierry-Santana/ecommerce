<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $mensagem = 'Informe e-mail e senha.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, Nome, Email, Senha, Tipo FROM Usuario WHERE Email = ? LIMIT 1');
        if (!$stmt) {
            $mensagem = 'Erro ao buscar usuário.';
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);

            if ($usuario && password_verify($senha, $usuario['Senha'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = (int) $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['Nome'];
                $_SESSION['usuario_email'] = $usuario['Email'];
                $_SESSION['usuario_tipo'] = $usuario['Tipo'];
                $_SESSION['Tipo'] = $usuario['Tipo'];
                $_SESSION['id'] = (int) $usuario['id'];

                if ($usuario['Tipo'] === 'admin') {
                    header('Location: ' . BASE_URL . 'telas/admin/tela_admin.php');
                    exit;
                }

                header('Location: ' . BASE_URL . 'index.php');
                exit;
            }

            $mensagem = 'E-mail ou senha incorretos.';
        }
    }
}
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <div class="card mx-auto shadow-sm" style="max-width: 420px;">
            <div class="card-body">
                <h2 class="mb-4 text-center">Login</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>usuario/login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
