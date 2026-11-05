<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem = '';
$mensagemTipo = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $dtnasc = trim($_POST['dtnasc'] ?? '');
    $dtnasc = $dtnasc === '' ? null : $dtnasc;
    $telefone = trim($_POST['telefone'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $confirmaremail = trim($_POST['confirmaremail'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirmarsenha = trim($_POST['confirmarsenha'] ?? '');

    if ($nome === '' || $sobrenome === '' || $email === '' || $senha === '') {
        $mensagem = 'Os campos nome, sobrenome, e-mail e senha são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Informe um e-mail válido.';
    } elseif ($email !== $confirmaremail) {
        $mensagem = 'Os e-mails informados não são iguais.';
    } elseif ($senha !== $confirmarsenha) {
        $mensagem = 'As senhas informadas não são iguais.';
    } else {
        $sqlExiste = 'SELECT id FROM Usuario WHERE Email = ? LIMIT 1';
        $stmtExiste = mysqli_prepare($conn, $sqlExiste);
        if (!$stmtExiste) {
            $mensagem = 'Erro ao preparar a verificação do e-mail.';
        } else {
            mysqli_stmt_bind_param($stmtExiste, 's', $email);
            mysqli_stmt_execute($stmtExiste);
            $resultadoExiste = mysqli_stmt_get_result($stmtExiste);

            if (mysqli_num_rows($resultadoExiste) > 0) {
                $mensagem = 'Este e-mail já está cadastrado.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sqlInsert = 'INSERT INTO Usuario (Nome, Sobrenome, Dtnasc, Telefone, CPF, Email, Senha, Foto_perfil, Produtos_comprados, Produtos_carrinho, produtos_desejo, Chat, Tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $stmtInsert = mysqli_prepare($conn, $sqlInsert);

                if (!$stmtInsert) {
                    $mensagem = 'Erro ao preparar o cadastro do usuário.';
                } else {
                    $fotoPerfil = '';
                    $produtosComprados = '';
                    $produtosCarrinho = '';
                    $produtosDesejo = '';
                    $chat = '';
                    $tipo = 'usuario';

                    mysqli_stmt_bind_param(
                        $stmtInsert,
                        'sssssssssssss',
                        $nome,
                        $sobrenome,
                        $dtnasc,
                        $telefone,
                        $cpf,
                        $email,
                        $senhaHash,
                        $fotoPerfil,
                        $produtosComprados,
                        $produtosCarrinho,
                        $produtosDesejo,
                        $chat,
                        $tipo
                    );

                    if (mysqli_stmt_execute($stmtInsert)) {
                        $usuarioId = (int) mysqli_insert_id($conn);
                        session_regenerate_id(true);
                        $_SESSION['usuario_id'] = $usuarioId;
                        $_SESSION['usuario_nome'] = $nome;
                        $_SESSION['usuario_email'] = $email;
                        $_SESSION['usuario_tipo'] = $tipo;
                        $_SESSION['Tipo'] = $tipo;
                        $_SESSION['id'] = $usuarioId;
                        $_SESSION['cadastro_sucesso'] = 'Usuário cadastrado com sucesso!';
                        header('Location: ' . BASE_URL . 'index.php');
                        exit;
                    } else {
                        $mensagem = 'Erro ao cadastrar usuário: ' . mysqli_error($conn);
                    }
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/../tela_cadastro/tela_cadastro.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php require_once __DIR__ . '/../include/navbar.php'; ?>

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

    <?php require_once __DIR__ . '/../include/footer.php'; ?>

    <script src="<?= BASE_URL ?>assets/js/formatacao.js"></script>
</body>
</html>
