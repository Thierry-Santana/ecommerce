<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    $_SESSION['login_erro'] = 'Faça login para ver seus produtos comprados.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$mensagemSucesso = $_SESSION['compra_sucesso'] ?? '';
unset($_SESSION['compra_sucesso'], $_SESSION['compra_id']);

$stmt = mysqli_prepare($conn, 'SELECT o.id AS compra_id, o.Data_compra, o.Status, o.Valor_total, pc.id_produto, pc.Imagem, pc.Nome, pc.Preco, pc.Frete, pc.Quantidade FROM Compra AS o INNER JOIN Produtos_comprados AS pc ON pc.id_compra = o.id WHERE o.id_usuario = ? AND pc.id_usuario = ? ORDER BY o.id DESC, pc.id ASC');
mysqli_stmt_bind_param($stmt, 'ii', $usuarioId, $usuarioId);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$compras = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $compraId = (int) $linha['compra_id'];
    if (!isset($compras[$compraId])) {
        $compras[$compraId] = [
            'Data_compra' => $linha['Data_compra'],
            'Status' => $linha['Status'],
            'Valor_total' => $linha['Valor_total'],
            'itens' => [],
        ];
    }
    $compras[$compraId]['itens'][] = $linha;
}
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meus produtos comprados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

    <main class="container py-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h2 mb-1">Meus produtos comprados</h1>
                <p class="text-secondary mb-0">Acompanhe seus pedidos e o status do pagamento.</p>
            </div>
            <a href="<?= BASE_URL ?>telas/carrinho/carrinho.php" class="btn btn-outline-light"><i class="bi bi-cart3 me-2"></i>Voltar ao carrinho</a>
        </div>

        <?php if ($mensagemSucesso !== ''): ?>
            <div class="alert alert-success" role="status">
                <?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($compras === []): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bag fs-1 text-secondary"></i>
                    <h2 class="h5 mt-3">Você ainda não tem produtos comprados.</h2>
                    <a href="<?= BASE_URL ?>index.php" class="btn btn-primary mt-2">Ver produtos</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($compras as $compraId => $compra): ?>
                <?php
                    $statusClasse = $compra['Status'] === 'Pago'
                        ? 'success'
                        : ($compra['Status'] === 'Em análise de pagamento' ? 'warning text-dark' : 'secondary');
                ?>
                <section class="card shadow-sm mb-4" aria-label="Compra número <?= (int) $compraId ?>">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h5 mb-1">Compra #<?= (int) $compraId ?></h2>
                            <span class="small text-secondary">Realizada em <?= htmlspecialchars(date('d/m/Y H:i', strtotime($compra['Data_compra'])), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <span class="badge bg-<?= $statusClasse ?> fs-6">Pagamento: <?= htmlspecialchars($compra['Status'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($compra['itens'] as $item): ?>
                            <?php
                                $imagem = $item['Imagem'] ?? '';
                                if ($imagem !== '' && !preg_match('/^https?:\/\//i', $imagem)) {
                                    $imagem = BASE_URL . ltrim($imagem, '/');
                                }
                                $quantidade = (int) $item['Quantidade'];
                                $subtotalItem = (float) $item['Preco'] * $quantidade;
                            ?>
                            <div class="row align-items-center g-3 py-3 border-bottom">
                                <div class="col-4 col-sm-3 col-md-2">
                                    <?php if ($imagem !== ''): ?>
                                        <img src="<?= htmlspecialchars($imagem, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['Nome'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded">
                                    <?php endif; ?>
                                </div>
                                <div class="col-8 col-sm-9 col-md-6">
                                    <h3 class="h6 mb-1"><?= htmlspecialchars($item['Nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="text-secondary mb-0">Quantidade: <?= $quantidade ?></p>
                                    <p class="text-secondary small mb-0">Preço unitário: R$ <?= number_format((float) $item['Preco'], 2, ',', '.') ?></p>
                                </div>
                                <div class="col-12 col-md-4 text-md-end">
                                    <span class="fw-bold">R$ <?= number_format($subtotalItem + (float) $item['Frete'], 2, ',', '.') ?></span>
                                    <span class="d-block small text-secondary">inclui frete de R$ <?= number_format((float) $item['Frete'], 2, ',', '.') ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="d-flex justify-content-between align-items-center fw-bold fs-5 pt-3">
                            <span>Total da compra</span>
                            <span>R$ <?= number_format((float) $compra['Valor_total'], 2, ',', '.') ?></span>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . '/../../include/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
