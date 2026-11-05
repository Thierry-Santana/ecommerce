<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['login_erro'] = 'Método de requisição inválido.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if ($email === '' || $senha === '') {
    $_SESSION['login_erro'] = 'Informe e-mail e senha.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT id, Nome, Email, Senha, Tipo FROM Usuario WHERE Email = ? LIMIT 1');
if (!$stmt) {
    $_SESSION['login_erro'] = 'Erro ao consultar o usuário.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario || !password_verify($senha, $usuario['Senha'])) {
    $_SESSION['login_erro'] = 'E-mail ou senha incorretos.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

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
