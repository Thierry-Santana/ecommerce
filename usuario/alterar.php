<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';
require_once __DIR__ . '/../include/produto.php';

$id = (int) ($_GET['id'] ?? 0);
$mensagem = '';
$mensagemTipo = 'danger';

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

$stmtUsuario = mysqli_prepare($conn, 'SELECT * FROM Usuario WHERE id = ? LIMIT 1');
if (!$stmtUsuario) {
    die('Erro ao preparar consulta do usuário: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtUsuario, 'i', $id);
mysqli_stmt_execute($stmtUsuario);
$usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUsuario));

if (!$usuario) {
    header('Location: listar.php');
    exit;
}

$fotoPerfil = $usuario['Foto_perfil'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $dtnasc = trim($_POST['dtnasc'] ?? '');
    $dtnasc = $dtnasc === '' ? null : $dtnasc;
    $telefone = trim($_POST['telefone'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tipo = trim($_POST['tipo'] ?? 'usuario');
    $senha = trim($_POST['senha'] ?? '');
    $arquivoFoto = $_FILES['foto_perfil'] ?? null;

    if ($arquivoFoto && $arquivoFoto['error'] !== UPLOAD_ERR_NO_FILE) {
        $validacaoFoto = validarUploadImagem($arquivoFoto);
        if (!$validacaoFoto['ok']) {
            $mensagem = $validacaoFoto['mensagem'];
        } elseif ((int) $arquivoFoto['size'] > 2 * 1024 * 1024) {
            $mensagem = 'A foto de perfil deve ter no máximo 2 MB.';
        } else {
            $nomeArquivo = 'perfil_usuario_' . $id . '_' . bin2hex(random_bytes(8)) . '.' . $validacaoFoto['extensao'];
            $diretorioImagens = prepararDiretorioProdutos();
            $caminhoArquivo = $diretorioImagens === null ? '' : $diretorioImagens . $nomeArquivo;
            if ($diretorioImagens === null || !move_uploaded_file($arquivoFoto['tmp_name'], $caminhoArquivo)) {
                $mensagem = 'Não foi possível salvar a foto de perfil do usuário.';
            } else {
                $fotoPerfil = 'assets/img/' . $nomeArquivo;
            }
        }
    }

    if ($mensagem === '' && !in_array($tipo, ['usuario', 'vendedor', 'admin'], true)) {
        $mensagem = 'Tipo de usuário inválido.';
    } elseif ($mensagem === '' && $id === (int) ($_SESSION['usuario_id'] ?? 0) && $tipo !== 'admin') {
        $mensagem = 'O administrador atualmente logado não pode perder essa permissão.';
    }

    if ($mensagem === '' && ($nome === '' || $email === '')) {
        $mensagem = 'Nome e e-mail são obrigatórios.';
    }

    if ($mensagem === '') {
        $stmtExiste = mysqli_prepare($conn, 'SELECT id FROM Usuario WHERE Email = ? AND id != ? LIMIT 1');
        if (!$stmtExiste) {
            $mensagem = 'Erro ao verificar e-mail do usuário.';
        } else {
            mysqli_stmt_bind_param($stmtExiste, 'si', $email, $id);
            mysqli_stmt_execute($stmtExiste);
            $resultadoExiste = mysqli_stmt_get_result($stmtExiste);

            if (mysqli_num_rows($resultadoExiste) > 0) {
                $mensagem = 'Este e-mail já pertence a outro usuário.';
            }
        }
    }

    if ($mensagem === '') {
        if ($senha !== '') {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = 'UPDATE Usuario SET Nome = ?, Sobrenome = ?, Dtnasc = ?, Telefone = ?, CPF = ?, Email = ?, Foto_perfil = ?, Senha = ?, Tipo = ? WHERE id = ?';
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                $mensagem = 'Erro ao preparar atualização do usuário.';
            } else {
                mysqli_stmt_bind_param($stmt, 'sssssssssi', $nome, $sobrenome, $dtnasc, $telefone, $cpf, $email, $fotoPerfil, $senhaHash, $tipo, $id);
            }
        } else {
            $sql = 'UPDATE Usuario SET Nome = ?, Sobrenome = ?, Dtnasc = ?, Telefone = ?, CPF = ?, Email = ?, Foto_perfil = ?, Tipo = ? WHERE id = ?';
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                $mensagem = 'Erro ao preparar atualização do usuário.';
            } else {
                mysqli_stmt_bind_param($stmt, 'sssssssi', $nome, $sobrenome, $dtnasc, $telefone, $cpf, $email, $fotoPerfil, $tipo, $id);
            }
        }

        if ($mensagem === '') {
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = 'Usuário atualizado com sucesso.';
                $mensagemTipo = 'success';
                $usuario['Nome'] = $nome;
                $usuario['Sobrenome'] = $sobrenome;
                $usuario['Dtnasc'] = $dtnasc;
                $usuario['Telefone'] = $telefone;
                $usuario['CPF'] = $cpf;
                $usuario['Email'] = $email;
                $usuario['Foto_perfil'] = $fotoPerfil;
                $usuario['Tipo'] = $tipo;
            } else {
                $mensagem = 'Erro ao atualizar usuário: ' . mysqli_error($conn);
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
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h2 class="mb-4">Editar usuário</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" action="alterar.php?id=<?= (int) $id ?>" enctype="multipart/form-data">
                    <div class="mb-4 text-center">
                        <?php if (!empty($usuario['Foto_perfil'])): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($usuario['Foto_perfil'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil do usuário" class="rounded-circle border border-2 border-light" style="width: 96px; height: 96px; object-fit: cover;">
                        <?php else: ?>
                            <img src="<?= BASE_URL ?>assets/img/perfil.png" alt="Foto padrão de perfil" class="rounded-circle border border-2 border-light" style="width: 96px; height: 96px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="mt-3">
                            <label for="foto_perfil" class="form-label d-block">Foto de perfil</label>
                            <input type="file" id="foto_perfil" name="foto_perfil" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text text-dark">JPG, PNG ou WEBP, até 2 MB.</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sobrenome</label>
                            <input type="text" name="sobrenome" class="form-control" value="<?= htmlspecialchars($usuario['Sobrenome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data de nascimento</label>
                            <input type="date" name="dtnasc" class="form-control" value="<?= htmlspecialchars($usuario['Dtnasc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" class="form-control" maxlength="15" value="<?= htmlspecialchars($usuario['Telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" class="form-control" maxlength="14" value="<?= htmlspecialchars($usuario['CPF'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['Email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Nova senha</label>
                            <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="usuario" <?= (($usuario['Tipo'] ?? 'usuario') === 'usuario') ? 'selected' : '' ?>>Usuário</option>
                                <option value="vendedor" <?= (($usuario['Tipo'] ?? 'usuario') === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
                                <option value="admin" <?= (($usuario['Tipo'] ?? 'usuario') === 'admin') ? 'selected' : '' ?>>Admin</option>
                            </select>
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

    <script src="<?= BASE_URL ?>assets/js/formatacao.js"></script>

</body>

</html>