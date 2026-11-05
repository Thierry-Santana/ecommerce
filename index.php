<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/conexao.php';
require_once __DIR__ . '/include/produto.php';

$produtos = buscarProdutos($conn);
$secoes = [
    ['titulo' => 'Ofertas', 'icone' => 'oferta.png', 'tamanho' => '35px'],
    ['titulo' => 'Mais Vendidos', 'icone' => 'estrela.png', 'tamanho' => '50px'],
    ['titulo' => 'PC Gamer', 'icone' => 'pc.png', 'tamanho' => '50px'],
];
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <title>E-commerce</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= BASE_URL ?>index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    
        <?php require_once __DIR__ . '/include/navbar.php'; ?>
        
    <main>
        <?php foreach ($secoes as $secao): ?>
            <section class="container mt-4">
                <h2 class="<?= $secao['titulo'] === 'Ofertas' ? '' : 'mt-5' ?>">
                    <img src="<?= BASE_URL ?>assets/img/<?= $secao['icone'] ?>" alt="" style="width: <?= $secao['tamanho'] ?>; height: <?= $secao['tamanho'] ?>;" class="mb-2">
                    <?= htmlspecialchars($secao['titulo'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <div class="produtos-carousel">
                    <button class="carousel-btn prev btnPrev" type="button" aria-label="Produtos anteriores"><i class="bi bi-chevron-left"></i></button>
                    <div class="produtos-wrapper">
                        <div class="produtos-lista">
                            <?php foreach ($produtos as $produto): ?>
                                <?= renderizarCardProduto($produto, 'produto border-2 custom-card-border') ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="carousel-btn next btnNext" type="button" aria-label="Próximos produtos"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>
        <?php endforeach; ?>

        <?php if (false): ?>
        <div class="container mt-4">

            <!--LINHA 1-->
            <h2><img src="<?= BASE_URL ?>assets/img/oferta.png" alt="" style="width: 35px; height: 35px;" class="mb-2"> Ofertas</h2>

            <div class="produtos-carousel">

                <button class="carousel-btn prev btnPrev">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="produtos-wrapper">

                    <div class="produtos-lista" id="produtosLista">

                        <!--PRODUTOS-->
                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/RTX5090.png" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">RTX 5090</h5>

                                    <p class="card-text">Placa de Vídeo ASUS RTX5090 ROG Astral Gaming OC NVIDIA GeForce, 32GB, GDDR7, ARGB, G-Sync, Ray Tracing, DLSS 4, HDR - 90YV0LW0-M0NA00</p>

                                    <h4 class="text-success fw-bold">R$ 24.999,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=1" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Monitor LG Ultragear</h5>

                                    <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync, DisplayPort, HDMI - 24gs60f-b</p>

                                    <h4 class="text-success fw-bold">R$ 999,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/mousegamersemfio.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Mouse Gamer</h5>

                                    <p class="card-text">Mouse Gamer Sem Fio Logitech G305 LIGHTSPEED, 12000 DPI, 6 Botões, Preto - 910-005281</p>

                                    <h4 class="text-success fw-bold">R$ 549,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=3" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/ryzen75800x3d.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Ryzen 7 5800X3D</h5>

                                    <p class="card-text">Processador AMD Ryzen 7 5800X3D, 3.4GHZ, 8-CORE, Cache 100MB, AM4 - 100-100000651POF</p>

                                    <h4 class="text-success fw-bold">R$ 2.599,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=4" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/ssd.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">SSD NVME</h5>

                                    <p class="card-text">SSD Kingston NV3, 1 TB, M.2 2280, PCIe 4.0 x4, NVMe, Leitura: 6000 MB/s, Gravação: 4000 MB/s, Azul - SNV3S/1000G</p>

                                    <h4 class="text-success fw-bold">R$ 1.094,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=5" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/smarttv.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Smart TV TCL</h5>

                                    <p class="card-text">Smart TV TCL 55 Polegadas QLED Mini LED 4K C6K WiFi Bluetooth Google TV 4 HDMI 144Hz HDR10+ 55C6K</p>

                                    <h4 class="text-success fw-bold">R$ 3.234,67</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=6" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/pcgamer.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">PC Gamer</h5>

                                    <p class="card-text">PC GAMER CAPTAIN AMD Ryzen 5 5500 / AMD Radeon RX 9060 XT / 16GB DDR4 / SSD 480GB</p>

                                    <h4 class="text-success fw-bold">R$ 5.899,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=7" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/alexa.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Alexa</h5>

                                    <p class="card-text">Echo Dot 5ª geração Amazon, com Alexa, Smart Speaker, Preto - B09B8VGCR8</p>

                                    <h4 class="text-success fw-bold">R$ 399,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=8" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/cadeiragamer.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Cadeira Gamer</h5>

                                    <p class="card-text">Cadeira Gamer XPG NEXUS, Até 125kg, Ajuste de Altura, Encosto Reclinável, Preto e Vermelho - NEXUS-RDCWW</p>

                                    <h4 class="text-success fw-bold">R$ 899,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=9" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-btn next btnNext">
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>

            <!--LINHA 2-->
            <h2 class="mt-5"><img src="<?= BASE_URL ?>assets/img/estrela.png" alt="" style="width: 50px; height: 50px;" class="mb-2"> Mais Vendidos</h2>

            <div class="produtos-carousel">

                <button class="carousel-btn prev btnPrev">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="produtos-wrapper">

                    <div class="produtos-lista" id="produtosLista">

                        <!--PRODUTOS-->
                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/RTX5090.png" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">RTX 5090</h5>

                                    <p class="card-text">Placa de Vídeo ASUS RTX5090 ROG Astral Gaming OC NVIDIA GeForce, 32GB, GDDR7, ARGB, G-Sync, Ray Tracing, DLSS 4, HDR - 90YV0LW0-M0NA00</p>

                                    <h4 class="text-success fw-bold">R$ 24.999,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=1" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Monitor LG Ultragear</h5>

                                    <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync, DisplayPort, HDMI - 24gs60f-b</p>

                                    <h4 class="text-success fw-bold">R$ 999,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/mousegamersemfio.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Mouse Gamer</h5>

                                    <p class="card-text">Mouse Gamer Sem Fio Logitech G305 LIGHTSPEED, 12000 DPI, 6 Botões, Preto - 910-005281</p>

                                    <h4 class="text-success fw-bold">R$ 549,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=3" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/ryzen75800x3d.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Ryzen 7 5800X3D</h5>

                                    <p class="card-text">Processador AMD Ryzen 7 5800X3D, 3.4GHZ, 8-CORE, Cache 100MB, AM4 - 100-100000651POF</p>

                                    <h4 class="text-success fw-bold">R$ 2.599,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=4" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/ssd.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">SSD NVME</h5>

                                    <p class="card-text">SSD Kingston NV3, 1 TB, M.2 2280, PCIe 4.0 x4, NVMe, Leitura: 6000 MB/s, Gravação: 4000 MB/s, Azul - SNV3S/1000G</p>

                                    <h4 class="text-success fw-bold">R$ 1.094,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=5" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/smarttv.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Smart TV TCL</h5>

                                    <p class="card-text">Smart TV TCL 55 Polegadas QLED Mini LED 4K C6K WiFi Bluetooth Google TV 4 HDMI 144Hz HDR10+ 55C6K</p>

                                    <h4 class="text-success fw-bold">R$ 3.234,67</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=6" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/pcgamer.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">PC Gamer</h5>

                                    <p class="card-text">PC GAMER CAPTAIN AMD Ryzen 5 5500 / AMD Radeon RX 9060 XT / 16GB DDR4 / SSD 480GB</p>

                                    <h4 class="text-success fw-bold">R$ 5.899,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=7" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/alexa.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Alexa</h5>

                                    <p class="card-text">Echo Dot 5ª geração Amazon, com Alexa, Smart Speaker, Preto - B09B8VGCR8</p>

                                    <h4 class="text-success fw-bold">R$ 399,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=8" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/cadeiragamer.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Cadeira Gamer</h5>

                                    <p class="card-text">Cadeira Gamer XPG NEXUS, Até 125kg, Ajuste de Altura, Encosto Reclinável, Preto e Vermelho - NEXUS-RDCWW</p>

                                    <h4 class="text-success fw-bold">R$ 899,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=9" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-btn next btnNext">
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>

            <!--LINHA 3-->
            <h2 class="mt-5"><img src="<?= BASE_URL ?>assets/img/pc.png" alt="" style="width: 50px; height: 50px;"> PC Gamer</h2>

            <div class="produtos-carousel">

                <button class="carousel-btn prev btnPrev">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="produtos-wrapper">

                    <div class="produtos-lista" id="produtosLista">

                        <!--PRODUTOS-->
                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/RTX5090.png" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">RTX 5090</h5>

                                    <p class="card-text">Placa de Vídeo ASUS RTX5090 ROG Astral Gaming OC NVIDIA GeForce, 32GB, GDDR7, ARGB, G-Sync, Ray Tracing, DLSS 4, HDR - 90YV0LW0-M0NA00</p>

                                    <h4 class="text-success fw-bold">R$ 24.999,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=1" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/monitorlg.png" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Monitor LG Ultragear</h5>

                                    <p class="card-text">Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync, DisplayPort, HDMI - 24gs60f-b</p>

                                    <h4 class="text-success fw-bold">R$ 999,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=2" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/mousegamersemfio.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Mouse Gamer</h5>

                                    <p class="card-text">Mouse Gamer Sem Fio Logitech G305 LIGHTSPEED, 12000 DPI, 6 Botões, Preto - 910-005281</p>

                                    <h4 class="text-success fw-bold">R$ 549,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=3" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/ryzen75800x3d.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Ryzen 7 5800X3D</h5>

                                    <p class="card-text">Processador AMD Ryzen 7 5800X3D, 3.4GHZ, 8-CORE, Cache 100MB, AM4 - 100-100000651POF</p>

                                    <h4 class="text-success fw-bold">R$ 2.599,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=4" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/ssd.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">SSD NVME</h5>

                                    <p class="card-text">SSD Kingston NV3, 1 TB, M.2 2280, PCIe 4.0 x4, NVMe, Leitura: 6000 MB/s, Gravação: 4000 MB/s, Azul - SNV3S/1000G</p>

                                    <h4 class="text-success fw-bold">R$ 1.094,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=5" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/smarttv.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Smart TV TCL</h5>

                                    <p class="card-text">Smart TV TCL 55 Polegadas QLED Mini LED 4K C6K WiFi Bluetooth Google TV 4 HDMI 144Hz HDR10+ 55C6K</p>

                                    <h4 class="text-success fw-bold">R$ 3.234,67</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=6" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/pcgamer.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">PC Gamer</h5>

                                    <p class="card-text">PC GAMER CAPTAIN AMD Ryzen 5 5500 / AMD Radeon RX 9060 XT / 16GB DDR4 / SSD 480GB</p>

                                    <h4 class="text-success fw-bold">R$ 5.899,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=7" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/alexa.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Alexa</h5>

                                    <p class="card-text">Echo Dot 5ª geração Amazon, com Alexa, Smart Speaker, Preto - B09B8VGCR8</p>

                                    <h4 class="text-success fw-bold">R$ 399,90</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=8" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="produto">
                            <div class="card border-2 custom-card-border position-relative h-100">
                                <img src="<?= BASE_URL ?>assets/img/cadeiragamer.webp" class="card-img-top" alt="">

                                <div class="card-body">
                                    <h5 class="card-title">Cadeira Gamer</h5>

                                    <p class="card-text">Cadeira Gamer XPG NEXUS, Até 125kg, Ajuste de Altura, Encosto Reclinável, Preto e Vermelho - NEXUS-RDCWW</p>

                                    <h4 class="text-success fw-bold">R$ 899,99</h4>

                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=9" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-btn next btnNext">
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>
        <?php endif; ?>
    </main>

        <?php require_once __DIR__ . '/include/footer.php'; ?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

    <script src="<?= BASE_URL ?>assets/js/carrosel.js"></script>
</body>

</html>
