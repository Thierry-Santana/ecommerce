<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = !empty($_SESSION['usuario_id']);
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Minha conta';
$tipoUsuario = $_SESSION['usuario_tipo'] ?? $_SESSION['Tipo'] ?? null;
$fotoPerfil = '';

if ($usuarioLogado) {
    $usuarioId = (int) $_SESSION['usuario_id'];
    $stmtFotoPerfil = mysqli_prepare($conn, 'SELECT Foto_perfil FROM Usuario WHERE id = ? LIMIT 1');

    if ($stmtFotoPerfil) {
        mysqli_stmt_bind_param($stmtFotoPerfil, 'i', $usuarioId);
        mysqli_stmt_execute($stmtFotoPerfil);
        $dadosFotoPerfil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtFotoPerfil));
        $fotoPerfil = $dadosFotoPerfil['Foto_perfil'] ?? '';
    }
}

if ($fotoPerfil !== '' && !preg_match('#^https?://#i', $fotoPerfil)) {
    $fotoPerfil = BASE_URL . ltrim(str_replace('/ecommerce/', '', $fotoPerfil), '/');
}
?>

<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid d-flex align-items-stretch">
            <a class="navbar-brand d-flex align-items-center me-4 fw-bold" href="<?= BASE_URL ?>index.php">E-Commerce</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse flex-column align-items-stretch" id="navbarSupportedContent">

                <!--PRIMEIRA LINHA-->
                <div class="d-flex justify-content-between align-items-center w-100">
                    <form action="<?= BASE_URL ?>telas/pesquisa/pesquisa.php" method="GET" class="input-group flex-grow-1 me-4">
                        <input class="form-control" type="search" placeholder="Pesquisar..." name="q">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                    <div class="d-flex align-items-center flex-shrink-0 me-4">
                        <?php if ($fotoPerfil !== ''): ?>
                            <img src="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;">
                        <?php else: ?>
                            <i class="bi bi-person-circle text-white fs-3 me-3"></i>
                        <?php endif; ?>

                        <div class="text-white">
                            <?php if ($usuarioLogado): ?>
                                <span class="small text-secondary">Olá,</span><br>
                                <a href="<?= BASE_URL ?>usuario/tela_usuario.php" class="text-white fw-bold">
                                    <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>tela_login/tela_login.php" class="text-white">
                                    Entre
                                </a>
                                ou
                                <br />
                                <a href="<?= BASE_URL ?>tela_cadastro/tela_cadastro.php" class="text-white fw-bold">
                                    cadastre-se
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($tipoUsuario === 'admin'): ?>
                        <div class="flex-shrink-0 me-3">
                            <a href="<?= BASE_URL ?>telas/admin/tela_admin.php" class="btn btn-primary btn-sm fw-bold">
                                Painel Admin
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="flex-shrink-0">
                        <a href="<?= BASE_URL ?>telas/lista_desejos/tela_lista_desejos.php" class="text-white">
                            <i class="bi bi-heart fs-4 me-3"></i>
                        </a>
                    </div>

                    <div class="flex-shrink-0">
                        <a href="<?= BASE_URL ?>telas/carrinho/carrinho.php" class="text-white">
                            <i class="bi bi-cart3 fs-4"></i>
                        </a>
                    </div>
                </div>

                <!--SEGUNDA LINHA-->
                <div class="d-flex w-100 mt-2">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle border border-light rounded" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Departamentos</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>telas/monitores/monitores.php">Monitores</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>telas/tv/tv.php">TV</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>telas/perifericos/perifericos.php">Perifericos</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>telas/ofertas/ofertas.php">Ofertas</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>telas/cadeiras/tela_cadeira.php">Cadeiras</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="<?= BASE_URL ?>telas/mais_vendidos/mais_vendidos.php">Mais Vendidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>telas/ofertas/ofertas.php">Ofertas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>telas/perifericos/perifericos.php">Periféricos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>telas/tv/tv.php">TV</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>telas/monitores/monitores.php">Monitores</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
