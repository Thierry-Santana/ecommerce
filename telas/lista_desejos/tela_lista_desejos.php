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
$mensagem = isset($_GET['removido']) ? 'Produto removido da lista de desejos.' : '';

$stmt = mysqli_prepare($conn, 'SELECT d.id, p.id AS id_produto, p.Imagem, p.Nome, p.Preco FROM Produto_desejo d INNER JOIN Produto p ON p.id = d.id_produto WHERE d.id_usuario = ? ORDER BY d.id DESC');
mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
mysqli_stmt_execute($stmt);
$itensDesejos = mysqli_stmt_get_result($stmt);
?>

<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
    <head>
        <title>Lista de Desejos</title>
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
                <h2 class="mb-4 text-center">Minha Lista de Desejos</h2>

                <?php if ($mensagem !== ''): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <div class="row g-4 justify-content-center">
                    <div class="col-lg-8">
                        <?php if (mysqli_num_rows($itensDesejos) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($itensDesejos)): ?>
                                <?php
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
                                            <div class="col-md-3">
                                                <h5><?= htmlspecialchars($item['Nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4 class="text-success fw-bold">R$ <?= number_format((float) $item['Preco'], 2, ',', '.') ?></h4>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <a href="<?= BASE_URL ?>carrinho/adicionar.php?id=<?= (int) $item['id_produto'] ?>" class="btn btn-success btn-sm">Adicionar ao carrinho</a>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="<?= BASE_URL ?>telas/lista_desejos/remover.php?id=<?= (int) $item['id_produto'] ?>" class="btn btn-danger btn-sm w-100" title="Remover da lista de desejos">
                                                    Remover
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <h5 class="mb-3">Sua lista de desejos está vazia</h5>
                                    <a href="<?= BASE_URL ?>index.php" class="btn btn-primary">Ver produtos</a>
                                </div>
                            </div>
                        <?php endif; ?>
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
