<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$mensagem = isset($_GET['adicionado']) ? 'Produto adicionado ao carrinho!' : '';
$erroCheckout = $_SESSION['checkout_erro'] ?? '';
unset($_SESSION['checkout_erro']);
$csrfToken = $_SESSION['checkout_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['checkout_csrf'] = $csrfToken;

$stmt = mysqli_prepare($conn, 'SELECT c.id, c.id_produto, p.Imagem, p.Nome, p.Preco, p.Frete, c.Quantidade, p.Estoque FROM Produtos_carrinho AS c INNER JOIN Produto AS p ON p.id = c.id_produto WHERE c.id_usuario = ? ORDER BY c.id DESC');
mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
mysqli_stmt_execute($stmt);
$itensCarrinho = mysqli_stmt_get_result($stmt);

$subtotal = 0.0;
$totalFrete = 0.0;

while ($item = mysqli_fetch_assoc($itensCarrinho)) {
    $subtotal += (float) $item['Preco'] * (int) $item['Quantidade'];
    $totalFrete += (float) $item['Frete'];
}

$totalPagar = $subtotal + $totalFrete;
?>

<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
    <head>
        <title>Carrinho</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>telas/carrinho/carrinho.css">
    </head>

    <body>
        <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

        <main>
            <div class="container py-5">
                <h2 class="mb-4">Meu Carrinho</h2>

                <?php if ($mensagem !== ''): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($erroCheckout !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erroCheckout, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <?php $itensCarrinho->data_seek(0); ?>
                        <?php if (mysqli_num_rows($itensCarrinho) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($itensCarrinho)): ?>
                                <?php
                                    $valorItem = (float) $item['Preco'] * (int) $item['Quantidade'];
                                    $imagemItem = $item['Imagem'] ?? '';
                                    if ($imagemItem !== '' && !preg_match('/^https?:\/\//i', $imagemItem)) {
                                        $imagemItem = BASE_URL . ltrim($imagemItem, '/');
                                    }
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-2 text-center">
                                                <img src="<?= htmlspecialchars($imagemItem, ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['Nome'], ENT_QUOTES, 'UTF-8') ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <h5><?= htmlspecialchars($item['Nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4 class="text-success fw-bold">R$ <?= number_format($valorItem, 2, ',', '.') ?></h4>
                                            </div>
                                            <div class="col-md-2">
                                                <form method="POST" action="<?= BASE_URL ?>carrinho/atualizar.php" class="d-flex align-items-center gap-2">
                                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                                    <input type="number" name="quantidade" class="form-control" value="<?= (int) $item['Quantidade'] ?>" min="1" required onchange="this.form.submit()">
                                                </form>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="<?= BASE_URL ?>carrinho/remover.php?id=<?= (int) $item['id'] ?>" class="btn btn-danger btn-sm">Remover</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <h5 class="mb-3">Seu carrinho está vazio</h5>
                                    <a href="<?= BASE_URL ?>index.php" class="btn btn-primary">Ver produtos</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h4>Resumo</h4>

                                <div class="d-flex justify-content-between">
                                    <p>Subtotal</p>
                                    <p>R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <p>Frete</p>
                                    <p>R$ <?= number_format($totalFrete, 2, ',', '.') ?></p>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between fw-bold fs-5">
                                    <p>Total a Pagar</p>
                                    <p>R$ <?= number_format($totalPagar, 2, ',', '.') ?></p>
                                </div>

                                <?php if (mysqli_num_rows($itensCarrinho) > 0): ?>
                                    <form method="POST" action="<?= BASE_URL ?>telas/compra/finalizar.php" class="mt-4">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="btn btn-success w-100">Finalizar Compra</button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success w-100 mt-4" disabled>Finalizar Compra</button>
                                <?php endif; ?>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <p>Pague com:</p>
                                    <img src="<?= BASE_URL ?>assets/img/pix.png" alt="" style="width: 58px; height: 38px" class="rounded">
                                    <img src="<?= BASE_URL ?>assets/img/visa.png" alt="" style="width: 58px; height: 38px" class="rounded">
                                    <img src="<?= BASE_URL ?>assets/img/mastercard.png" alt="" style="width: 58px; height: 38px" class="rounded">
                                    <img src="<?= BASE_URL ?>assets/img/american.png" alt="" style="width: 58px; height: 38px" class="rounded">
                                    <img src="<?= BASE_URL ?>assets/img/boleto.png" alt="" style="width: 58px; height: 38px" class="rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once __DIR__ . '/../../include/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
    </body>
</html>
