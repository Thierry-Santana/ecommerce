<?php require_once __DIR__ . '/../config.php'; ?>

<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <title>Cadastro</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= BASE_URL ?>tela_cadastro/tela_cadastro.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <?php require_once __DIR__ . '/../include/navbar.php'; ?>

    <main>
        <div class="centralizar">
            <div class="caixa">
                <h2>Crie sua conta pessoal</h2>
                <form action="<?= BASE_URL ?>usuario/cadastrar.php" method="POST">
                    <div class="container">

                        <div class="row">

                            <div class="col-md-6">
                                <label for="nome" class="form-label"></label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sobrenome" class="form-label"></label>
                                <input type="text" class="form-control" id="sobrenome" name="sobrenome" placeholder="Sobrenome" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dtnasc" class="form-label"></label>
                                <input type="date" class="form-control" id="dtnasc" name="dtnasc" placeholder="Data de Nascimento">
                            </div>
                            <div class="col-md-6">
                                <label for="telefone" class="form-label"></label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="Telefone" maxlength="15">
                            </div>
                            <div class="">
                                <label for="cpf" class="form-label"></label>
                                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF" maxlength="14">
                            </div>
                            <div class="">
                                <label for="email" class="form-label"></label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby="Email"
                                    placeholder="E-mail" required>
                            </div>
                            <div class="">
                                <label for="confirmaremail" class="form-label"></label>
                                <input type="email" class="form-control" id="confirmaremail" name="confirmaremail" aria-describedby="Email"
                                    placeholder="Digite novamente o E-mail" required>
                            </div>
                            <div class="col-md-6">
                                <label for="senha" class="form-label"></label>
                                <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirmarsenha" class="form-label"></label>
                                <input type="password" class="form-control" id="confirmarsenha" name="confirmarsenha"
                                    placeholder="Digite novamente a Senha" required>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success">Criar Conta</button>
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>tela_login/tela_login.php" class="btn btn-secondary" role="button">Logar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../include/footer.php'; ?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

    <script src="<?= BASE_URL ?>assets/js/formatacao.js"></script>
</body>

</html>