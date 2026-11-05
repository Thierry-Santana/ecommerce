<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$idAdministrador = (int) ($_SESSION['usuario_id'] ?? 0);
$tipoUsuario = $_SESSION['usuario_tipo'] ?? $_SESSION['Tipo'] ?? null;
$idInformado = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$ehExclusaoAdministrativa = $_SERVER['REQUEST_METHOD'] === 'GET' && $idInformado !== null;

if ($ehExclusaoAdministrativa) {
    require_once __DIR__ . '/../include/verificar_admin.php';
    $idUsuarioExcluir = $idInformado;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['id'])) {
    $idUsuarioExcluir = $idAdministrador;

    if ($tipoUsuario === 'admin') {
        header('Location: ' . BASE_URL . 'usuario/tela_usuario.php?erro=' . urlencode('O administrador atualmente logado não pode ser excluído.'));
        exit;
    }
} else {
    header('Location: listar.php?erro=' . urlencode('Solicitação de exclusão inválida.'));
    exit;
}

if ($idUsuarioExcluir === false || $idUsuarioExcluir === null || $idUsuarioExcluir <= 0) {
    header('Location: listar.php?erro=' . urlencode('ID de usuário inválido.'));
    exit;
}

if ($idUsuarioExcluir === $idAdministrador) {
    header('Location: listar.php?erro=' . urlencode('O administrador atualmente logado não pode ser excluído.'));
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM Usuario WHERE id = ?');
if (!$stmt) {
    header('Location: listar.php?erro=' . urlencode('Erro ao preparar exclusão do usuário.'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $idUsuarioExcluir);

if (mysqli_stmt_execute($stmt)) {
    header('Location: listar.php?sucesso=' . urlencode('Usuário excluído com sucesso.'));
    exit;
}

$error = mysqli_error($conn);
if (stripos($error, 'foreign key') !== false || stripos($error, 'FOREIGN KEY') !== false) {
    $mensagem = 'Não foi possível excluir este usuário porque ele está relacionado a outros registros do banco.';
} else {
    $mensagem = 'Erro ao excluir usuário: ' . $error;
}

header('Location: listar.php?erro=' . urlencode($mensagem));
exit;
