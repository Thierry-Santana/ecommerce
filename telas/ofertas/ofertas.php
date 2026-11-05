<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';
require_once __DIR__ . '/../../include/produto.php';
$produtos = buscarProdutos($conn, 4);
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
    <head>
        <title>Ofertas</title>
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
        <link rel="stylesheet" href="<?= BASE_URL ?>telas/ofertas/ofertas.css">
    </head>

    <body>

        <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

        <main>
            <div class="container mt-4">

            <h2 class="mt-4 mb-4 text-center"><img src="<?= BASE_URL ?>assets/img/oferta.png" alt="" style="width: 35px; height: 35px;" class="mb-2"> Ofertas</h2>

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
                                    <img src="<?= BASE_URL ?>assets/img/RTX5090.png" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">RTX 5090</h5>

                                        <p class="card-text">Placa de Vídeo ASUS RTX5090 ROG Astral Gaming OC NVIDIA GeForce, 32GB,
                                            GDDR7, ARGB, G-Sync, Ray Tracing, DLSS 4, HDR - 90YV0LW0-M0NA00</p>

                                        <h4 class="text-success fw-bold">R$ 24.999,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=1" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/mousegamersemfio.webp" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Mouse Gamer</h5>

                                        <p class="card-text">Mouse Gamer Sem Fio Logitech G305 LIGHTSPEED, 12000 DPI, 6 Botões, Preto - 910-005281</p>

                                        <h4 class="text-success fw-bold">R$ 549,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=3" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/alexa.webp" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Alexa</h5>

                                        <p class="card-text">Echo Dot 5ª geração Amazon, com Alexa, Smart Speaker, Preto - B09B8VGCR8</p>

                                        <h4 class="text-success fw-bold">R$ 399,90</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=8" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/ryzen75800x3d.webp" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">Ryzen 7 5800X3D</h5>

                                        <p class="card-text">Processador AMD Ryzen 7 5800X3D, 3.4GHZ, 8-CORE, Cache 100MB, AM4 - 100-100000651POF</p>

                                        <h4 class="text-success fw-bold">R$ 2.599,99</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=4" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card position-relative h-100">
                                    <img src="<?= BASE_URL ?>assets/img/ssd.webp" class="card-img-top" alt="">

                                    <div class="card-body">
                                        <h5 class="card-title">SSD NVME</h5>

                                        <p class="card-text">SSD Kingston NV3, 1 TB, M.2 2280, PCIe 4.0 x4, NVMe, Leitura: 6000 MB/s, Gravação: 4000 MB/s, Azul - SNV3S/1000G</p>

                                        <h4 class="text-success fw-bold">R$ 1.094,99</h4>

                                        <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=5" class="stretched-link"></a>
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
