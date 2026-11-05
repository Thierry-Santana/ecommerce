<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    $_SESSION['login_erro'] = 'Faça login para acessar sua conta.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$email = $_SESSION['usuario_email'] ?? '';
$primeiroNome = explode(' ', trim($nome))[0] ?: 'Usuário';
$fotoPerfil = '';

$stmtPerfil = mysqli_prepare($conn, 'SELECT Foto_perfil FROM Usuario WHERE id = ? LIMIT 1');
if ($stmtPerfil) {
    mysqli_stmt_bind_param($stmtPerfil, 'i', $usuarioId);
    mysqli_stmt_execute($stmtPerfil);
    $dadosPerfil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtPerfil));
    $fotoPerfil = $dadosPerfil['Foto_perfil'] ?? '';
}

$fotoPerfilUrl = $fotoPerfil;
if ($fotoPerfilUrl !== '' && !preg_match('#^https?://#i', $fotoPerfilUrl)) {
    $fotoPerfilUrl = BASE_URL . ltrim(str_replace('/ecommerce/', '', $fotoPerfilUrl), '/');
}

function contarRegistros(mysqli $conn, string $tabela, int $usuarioId): int
{
    $tabelasPermitidas = ['Produtos_carrinho', 'Produtos_comprados', 'Produto_desejo', 'Chat'];
    if (!in_array($tabela, $tabelasPermitidas, true)) {
        return 0;
    }

    $coluna = $tabela === 'Chat' ? 'id_usuario' : 'id_usuario';
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM {$tabela} WHERE {$coluna} = ?");
    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $linha = mysqli_fetch_assoc($resultado);

    return (int) ($linha['total'] ?? 0);
}

$quantidades = [
    'carrinho' => contarRegistros($conn, 'Produtos_carrinho', $usuarioId),
    'compras' => contarRegistros($conn, 'Produtos_comprados', $usuarioId),
    'desejos' => contarRegistros($conn, 'Produto_desejo', $usuarioId),
    'chats' => contarRegistros($conn, 'Chat', $usuarioId),
];
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minha conta | E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>usuario/tela_usuario.css">
</head>
<body>
    <?php require_once __DIR__ . '/../include/navbar.php'; ?>

    <main>
        <section class="cabecalho-conta py-5">
            <div class="container d-flex flex-wrap align-items-center justify-content-between gap-4">
                <div class="d-flex align-items-center gap-3">
                    <?php if ($fotoPerfil !== ''): ?>
                        <img src="<?= htmlspecialchars($fotoPerfilUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil" class="avatar-conta rounded-circle">
                    <?php else: ?>
                        <img src="<?= BASE_URL ?>assets/img/perfil.png" alt="Imagem padrão de perfil" class="avatar-conta rounded-circle">
                    <?php endif; ?>
                    <div>
                        <p class="text-uppercase text-secondary small mb-1">Minha conta</p>
                        <h1 class="h2 mb-1">Olá, <?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?>!</h1>
                        <p class="text-secondary mb-0"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>usuario/sair.php" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                </a>
            </div>
        </section>

        <section class="container py-5">
            <div class="mb-4">
                <h2 class="h4 mb-1">Acompanhe sua conta</h2>
                <p class="text-secondary mb-0">Acesse rapidamente seus produtos e pedidos.</p>
            </div>

            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <a class="link-conta" href="<?= BASE_URL ?>telas/carrinho/carrinho.php">
                        <div class="cartao-conta rounded-3 p-4 h-100">
                            <i class="bi bi-cart3 icone"></i>
                            <div class="quantidade-conta mt-3"><?= $quantidades['carrinho'] ?></div>
                            <h3 class="h6 mb-1">Itens no carrinho</h3>
                            <span class="small text-secondary">Ver carrinho <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="link-conta" href="<?= BASE_URL ?>telas/compra/listar.php">
                        <div class="cartao-conta rounded-3 p-4 h-100">
                            <i class="bi bi-bag-check icone"></i>
                            <div class="quantidade-conta mt-3"><?= $quantidades['compras'] ?></div>
                            <h3 class="h6 mb-1">Produtos comprados</h3>
                            <span class="small text-secondary">Ver pedidos <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="link-conta" href="<?= BASE_URL ?>telas/lista_desejos/tela_lista_desejos.php">
                        <div class="cartao-conta rounded-3 p-4 h-100">
                            <i class="bi bi-heart icone"></i>
                            <div class="quantidade-conta mt-3"><?= $quantidades['desejos'] ?></div>
                            <h3 class="h6 mb-1">Lista de desejos</h3>
                            <span class="small text-secondary">Continuar comprando <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </a>
                </div>
                <!-- 
                CHAT FOI COMENTADO PQ AINDA NÃO FOI IMPLEMENTADO, MAS DEIXEI O CÓDIGO AQUI PARA FUTURAMENTE SER USADO

                <div class="col-sm-6 col-lg-3">
                    <a class="link-conta" href="<?= BASE_URL ?>chat/listar.php">
                        <div class="cartao-conta rounded-3 p-4 h-100">
                            <i class="bi bi-chat-dots icone"></i>
                            <div class="quantidade-conta mt-3"><?= $quantidades['chats'] ?></div>
                            <h3 class="h6 mb-1">Conversas</h3>
                            <span class="small text-secondary">Abrir mensagens <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </a>
                </div> -->
            </div>

            <div class="cartao-conta cartao-perfil rounded-3 p-4 mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h2 class="h5 mb-1">Dados pessoais</h2>
                    <p class="text-secondary mb-0">Mantenha suas informações de acesso atualizadas.</p>
                </div>
                <a href="<?= BASE_URL ?>usuario/editar_perfil.php" class="btn btn-success">
                    <i class="bi bi-pencil me-2"></i>Editar dados
                </a>
            </div>

            <div class="cartao-conta cartao-perfil rounded-3 p-4 mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h2 class="h5 mb-1">Apagar conta</h2>
                    <p class="text-secondary mb-0">Aviso! Isso irá deletar sua conta permanentemente!</p>
                </div>
                <form action="<?= BASE_URL ?>usuario/deletar.php" method="POST" onsubmit="return confirm('Deseja realmente apagar sua conta?');">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Apagar conta
                    </button>
                </form>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../include/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
