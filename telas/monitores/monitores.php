<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';
require_once __DIR__ . '/../../include/produto.php';
$produtos = buscarProdutos($conn, 1);
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
    <head>
        <title>Monitores</title>
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
        <link rel="stylesheet" href="<?= BASE_URL ?>telas/monitores/monitores.css">
    </head>

    <body>

        <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

        <main>
            <div class="container mt-4">

            <h2 class="mt-4 mb-4 text-center"><img src="<?= BASE_URL ?>assets/img/monitor.png" alt="" style="width: 60px; height: 60px;" class="mb-2"> Monitores</h2>

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
                                    <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Monitor LG Ultragear</h5>

                                        <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync,
                                            DisplayPort, HDMI - 24gs60f-b</p>

                                        <h4 class="text-success fw-bold">R$ 999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Monitor LG Ultragear</h5>

                                        <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync,
                                            DisplayPort, HDMI - 24gs60f-b</p>

                                        <h4 class="text-success fw-bold">R$ 999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Monitor LG Ultragear</h5>

                                        <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync,
                                            DisplayPort, HDMI - 24gs60f-b</p>

                                        <h4 class="text-success fw-bold">R$ 999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Monitor LG Ultragear</h5>

                                        <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync,
                                            DisplayPort, HDMI - 24gs60f-b</p>

                                        <h4 class="text-success fw-bold">R$ 999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Monitor LG Ultragear</h5>

                                        <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync,
                                            DisplayPort, HDMI - 24gs60f-b</p>

                                        <h4 class="text-success fw-bold">R$ 999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Monitor LG Ultragear</h5>

                                        <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync,
                                            DisplayPort, HDMI - 24gs60f-b</p>

                                        <h4 class="text-success fw-bold">R$ 999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
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
