<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';
require_once __DIR__ . '/../../include/produto.php';
$produtos = buscarProdutos($conn, 2);
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
    <head>
        <title>TV</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>telas/tv/tv.css">
    </head>

    <body>

        <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

        <main>
            <div class="container mt-4">

            <h2 class="mt-4 mb-4 text-center"><img src="<?= BASE_URL ?>assets/img/tv.png" alt="" style="width: 60px; height: 60px;" class="mb-1"> TV</h2>

                <div class="row">

                    <div class="col-md-3">
                        <?php require_once __DIR__ . '/../../include/sidebar.php'; ?>
                    </div>

                    <div class="col-md-9">

                        <div class="row g-3">
                            <?php foreach ($produtos as $produto): ?>
                                <?= renderizarCardProduto($produto, 'col-md-3') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php if (false): ?>
                        <!--LINHA 1-->
                        <div class="row g-3">

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/smarttv.webp" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Smart TV TCL</h5>

                                        <p class="card-text">Smart TV TCL 55 Polegadas QLED Mini LED 4K C6K WiFi Bluetooth Google TV 4 HDMI 144Hz HDR10+ 55C6K</p>

                                        <h4 class="text-success fw-bold">R$ 3.234,67</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=6" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once __DIR__ . '/../../include/footer.php'; ?>

        <!-- Bootstrap JavaScript Bundle (includes Popper) -->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
