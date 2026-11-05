<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/produto.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$mensagem = '';
$mensagemTipo = 'danger';

$stmtUsuario = mysqli_prepare($conn, 'SELECT Nome, Email, Senha, Foto_perfil, Telefone, Dtnasc, CPF FROM Usuario WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmtUsuario, 'i', $usuarioId);
mysqli_stmt_execute($stmtUsuario);
$usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUsuario));

if (!$usuario) {
    $_SESSION = [];
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $dtnasc = trim($_POST['dtnasc'] ?? '');
    $dtnasc = $dtnasc === '' ? null : $dtnasc;
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';

    $fotoPerfil = $usuario['Foto_perfil'] ?? '';
    $arquivoFoto = $_FILES['foto_perfil'] ?? null;

    if ($arquivoFoto && $arquivoFoto['error'] !== UPLOAD_ERR_NO_FILE) {
        $validacaoFoto = validarUploadImagem($arquivoFoto);
        if (!$validacaoFoto['ok']) {
            $mensagem = $validacaoFoto['mensagem'];
        } elseif ((int) $arquivoFoto['size'] > 2 * 1024 * 1024) {
            $mensagem = 'A foto de perfil deve ter no máximo 2 MB.';
        } else {
            $nomeArquivo = 'perfil_usuario_' . $usuarioId . '_' . bin2hex(random_bytes(8)) . '.' . $validacaoFoto['extensao'];
            $diretorioImagens = prepararDiretorioProdutos();
            $caminhoArquivo = $diretorioImagens === null ? '' : $diretorioImagens . $nomeArquivo;

            if ($diretorioImagens === null || !move_uploaded_file($arquivoFoto['tmp_name'], $caminhoArquivo)) {
                $mensagem = 'Não foi possível salvar a foto de perfil.';
            } else {
                $fotoPerfil = 'assets/img/' . $nomeArquivo;
            }
        }
    }

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Informe um nome e um e-mail válido.';
    } elseif ($email !== $usuario['Email']) {
        $stmtExiste = mysqli_prepare($conn, 'SELECT id FROM Usuario WHERE Email = ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($stmtExiste, 'si', $email, $usuarioId);
        mysqli_stmt_execute($stmtExiste);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtExiste)) > 0) {
            $mensagem = 'Este e-mail já está sendo usado por outra conta.';
        }
    }

    if ($mensagem === '' && $novaSenha !== '' && !password_verify($senhaAtual, $usuario['Senha'])) {
        $mensagem = 'Informe sua senha atual para criar uma nova senha.';
    }

    if ($mensagem === '') {
        if ($novaSenha !== '') {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmtAtualizar = mysqli_prepare($conn, 'UPDATE Usuario SET Nome = ?, Email = ?, Telefone = ?, Dtnasc = ?, Foto_perfil = ?, Senha = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmtAtualizar, 'ssssssi', $nome, $email, $telefone, $dtnasc, $fotoPerfil, $senhaHash, $usuarioId);
        } else {
            $stmtAtualizar = mysqli_prepare($conn, 'UPDATE Usuario SET Nome = ?, Email = ?, Telefone = ?, Dtnasc = ?, Foto_perfil = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmtAtualizar, 'sssssi', $nome, $email, $telefone, $dtnasc, $fotoPerfil, $usuarioId);
        }

        if (mysqli_stmt_execute($stmtAtualizar)) {
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;
            $usuario['Nome'] = $nome;
            $usuario['Email'] = $email;
            $usuario['Telefone'] = $telefone;
            $usuario['Dtnasc'] = $dtnasc;
            $usuario['Foto_perfil'] = $fotoPerfil;
            $mensagem = 'Dados atualizados com sucesso.';
            $mensagemTipo = 'success';
        } else {
            $mensagem = 'Não foi possível atualizar seus dados.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar perfil | E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>usuario/editar_perfil.css">
</head>
<body class="bg-dark">
    <?php require_once __DIR__ . '/../include/navbar.php'; ?>
    <main class="container py-5">
        <div class="caixa-perfil mx-auto">
            <div class="card-body p-4 p-md-5">
                <div class="cabecalho-perfil mb-4">
                    <h1 class="h2 mb-1">Editar perfil</h1>
                    <p class="text-secondary mb-0">Atualize suas informações pessoais.</p>
                </div>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>" role="alert">
                        <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="foto-perfil text-center mb-4">
                        <?php if (!empty($usuario['Foto_perfil'])): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($usuario['Foto_perfil'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil atual" class="previa-foto">
                        <?php else: ?>
                            <img src="<?= BASE_URL ?>assets/img/perfil.png" alt="Imagem padrão de perfil" class="previa-foto">
                        <?php endif; ?>
                        <label for="foto_perfil" class="form-label d-block mt-3">Foto de perfil</label>
                        <input type="file" id="foto_perfil" name="foto_perfil" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">JPG, PNG ou WEBP, até 2 MB.</div>
                    </div>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['Nome'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['Email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" class="form-control" maxlength="15" value="<?= htmlspecialchars($usuario['Telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="dtnasc" class="form-label">Data de nascimento</label>
                            <input type="date" id="dtnasc" name="dtnasc" class="form-control" value="<?= htmlspecialchars($usuario['Dtnasc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-12">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" id="cpf" class="form-control" value="<?= htmlspecialchars($usuario['CPF'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly>
                            <div class="form-text">O CPF não pode ser alterado.</div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <p class="text-secondary small">Para trocar a senha, preencha os dois campos abaixo.</p>
                    <div class="mb-3">
                        <label for="senha_atual" class="form-label">Senha atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label for="nova_senha" class="form-label">Nova senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" class="form-control">
                    </div>
                    <div class="acoes-perfil d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-2"></i>Salvar alterações</button>
                        <a href="<?= BASE_URL ?>usuario/tela_usuario.php" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php require_once __DIR__ . '/../include/footer.php'; ?>
</body>
</html>
