<?php
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$mensagem = '';
$tipoMensagem = '';
$email = '';
$conn = null;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
	require_once __DIR__ . '/../../database/conexao.php';
} catch (Throwable $erro) {
	$mensagem = 'Não foi possível verificar o e-mail no momento. Tente novamente mais tarde.';
	$tipoMensagem = 'danger';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = trim($_POST['email'] ?? '');

	if ($email === '') {
		$mensagem = 'Informe seu e-mail.';
		$tipoMensagem = 'danger';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$mensagem = 'Informe um endereço de e-mail válido.';
		$tipoMensagem = 'danger';
	} elseif ($conn instanceof mysqli) {
		try {
			$stmt = mysqli_prepare($conn, 'SELECT id FROM Usuario WHERE Email = ? LIMIT 1');
			mysqli_stmt_bind_param($stmt, 's', $email);
			mysqli_stmt_execute($stmt);
			$resultado = mysqli_stmt_get_result($stmt);
			$emailExiste = mysqli_fetch_assoc($resultado) !== null;
			mysqli_stmt_close($stmt);

			if ($emailExiste) {
				$mensagem = 'E-mail encontrado. Existe uma conta cadastrada com este endereço.';
				$tipoMensagem = 'success';
			} else {
				$mensagem = 'Não encontramos uma conta cadastrada com este e-mail.';
				$tipoMensagem = 'warning';
			}
		} catch (Throwable $erro) {
			$mensagem = 'Não foi possível verificar o e-mail no momento. Tente novamente mais tarde.';
			$tipoMensagem = 'danger';
		}
	}
}
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Esqueci minha senha</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MP8K8M2HN" crossorigin="anonymous">
	<link rel="stylesheet" href="<?= BASE_URL ?>tela_login/tela_login.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>telas/esqueceu_senha/tela_esqueceu_senha.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
	<?php if ($conn instanceof mysqli) { require_once __DIR__ . '/../../include/navbar.php'; } ?>

	<main class="centralizar recuperacao-page">
		<section class="caixa recuperacao-card" aria-labelledby="titulo-recuperacao">
			<h2 id="titulo-recuperacao">Esqueci minha senha</h2>
			<p class="text-center text-body-secondary mb-4">Digite o e-mail cadastrado para verificar se existe uma conta associada.</p>

			<?php if ($mensagem !== ''): ?>
				<div class="alert alert-<?= htmlspecialchars($tipoMensagem, ENT_QUOTES, 'UTF-8') ?>" role="status">
					<?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
				</div>
			<?php endif; ?>

			<form method="POST" action="<?= BASE_URL ?>telas/esqueceu_senha/tela_esqueceu_senha.php">
				<div class="mb-3">
					<label for="email" class="form-label">E-mail</label>
					<input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" placeholder="Digite seu e-mail" autocomplete="email" required>
				</div>
				<div class="d-grid gap-2 mt-4">
					<button type="submit" class="btn btn-success">Verificar e-mail</button>
				</div>
			</form>

			<p class="text-center mt-4 mb-0"><a href="<?= BASE_URL ?>tela_login/tela_login.php" class="link-light">← Voltar para o login</a></p>
		</section>
	</main>

	<?php require_once __DIR__ . '/../../include/footer.php'; ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
