<?php require_once __DIR__ . '/../config.php'; ?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>index.php">E-Commerce</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= BASE_URL ?>telas/admin/tela_admin.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>produto/listar.php">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>categoria/listar.php">Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>usuario/listar.php">Usuários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>vendedor/listar.php">Vendedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>telas/pedidos/listar.php">Pedidos</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <span class="text-light-emphasis small">Admin</span>
                    <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>index.php">Loja</a>
                    <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>usuario/sair.php">Sair</a>
                </div>
            </div>
        </div>
    </nav>
</header>