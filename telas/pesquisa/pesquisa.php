<?php
require_once __DIR__ . '/../../database/conexao.php';
require_once __DIR__ . '/../../config.php';

$q = trim($_GET['q'] ?? '');
$termo = '%' . $q . '%';
$stmt = mysqli_prepare($conn, 'SELECT id, Nome, Imagem, Preco, Descricao FROM Produto WHERE Nome LIKE ? ORDER BY id DESC');
$resultado = false;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $termo);
    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);
    }
}

?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
    <head>
        <title>Mais Vendidos</title>
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
        <link rel="stylesheet" href="<?= BASE_URL ?>telas/mais_vendidos/mais_vendidos.css">
    </head>

    <body>

        <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

        <main>
            <div class="container mt-4">

            <h2 class="mt-4 mb-4 text-center"><img src="<?= BASE_URL ?>assets/img/estrela.png" alt="" style="width: 50px; height: 50px;" class="mb-2"> Você pesquisou por: <?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></h2>

                <div class="row">

                    <div class="col-md-3">
                        <?php require_once __DIR__ . '/../../include/sidebar.php'; ?>
                    </div>

                    <div class="col-md-9">

                        <!--LINHA 1-->
                            <div class="row g-3">
                                <?php
                                if ($resultado && mysqli_num_rows($resultado) > 0) {
                                    while ($produto = mysqli_fetch_assoc($resultado)) {
                                        $imagemProduto = (string) ($produto['Imagem'] ?? '');
                                        if ($imagemProduto !== '' && !preg_match('#^https?://#i', $imagemProduto)) {
                                            $imagemProduto = BASE_URL . ltrim(str_replace('/ecommerce/', '', $imagemProduto), '/');
                                        }
                                        ?>
                                        <div class="col-md-3">
                                            <div class="card position-relative h-100">
                                                <img src="<?= htmlspecialchars($imagemProduto, ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars((string) $produto['Nome'], ENT_QUOTES, 'UTF-8') ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars((string) $produto['Nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                                                    <p class="card-text"><?= htmlspecialchars((string) $produto['Descricao'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <h4 class="text-success fw-bold">
                                                        R$ <?= number_format($produto['Preco'], 2, ',', '.') ?>
                                                    </h4>
                                                    <a href="<?= BASE_URL ?>telas/compra/tela_compra.php?id=<?= $produto['id'] ?>" class="stretched-link"></a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo '<div class="col-12">Nenhum produto encontrado.</div>';
                                }
                                ?>
                            </div>
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
