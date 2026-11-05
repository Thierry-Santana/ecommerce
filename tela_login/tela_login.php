<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erroLogin = $_SESSION['login_erro'] ?? null;
unset($_SESSION['login_erro']);
$cadastroSucesso = $_SESSION['cadastro_sucesso'] ?? null;
unset($_SESSION['cadastro_sucesso']);
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
    <head>
        <title>Login</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="<?= BASE_URL ?>tela_login/tela_login.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>

    <body>
        
        <?php require_once __DIR__ . '/../include/navbar.php'; ?>
        
        <main>
            <div class="centralizar">
                <div class="caixa">
                    <h2>Bem-vindo(a) de volta!</h2>

                    <?php if ($cadastroSucesso): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($cadastroSucesso, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($erroLogin): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($erroLogin, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>tela_login/validar_login.php">
                        <div class="container">

                            <div class="row">

                                <div class="">
                                    <label for="email" class="form-label"></label>
                                    <input type="email" class="form-control" id="email" name="email" aria-describedby="email" placeholder="Digite o e-mail, CPF ou CNPJ" required>
                                </div>
                                <div class="">
                                    <label for="senha" class="form-label"></label>
                                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha" required>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success">Entrar</button>
                                </div>

                                <hr class="my-4">

                                <div class="d-grid gap-2">
                                    <a href="<?= BASE_URL ?>tela_cadastro/tela_cadastro.php" class="btn btn-secondary" role="button">Criar Conta</a>
                                </div>
                                <p class="text-center mt-2">Esqueceu a senha? <a href="<?= BASE_URL ?>telas/esqueceu_senha/tela_esqueceu_senha.php">Clique aqui</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
            <?php require_once __DIR__ . '/../include/footer.php'; ?>
        
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
