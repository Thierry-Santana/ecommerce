<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/conexao.php';
require_once __DIR__ . '/../../include/produto.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = null;

if ($id === false || $id === null || $id <= 0) {
    http_response_code(404);
    echo 'Produto não encontrado.';
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);
$stmt = mysqli_prepare(
    $conn,
    'SELECT id, Nome, Imagem, Preco, Frete, Estoque, Descricao, id_categoria FROM Produto WHERE id = ? LIMIT 1'
);

if (!$stmt) {
    error_log('[ecommerce compra] Falha ao preparar consulta do produto: ' . mysqli_error($conn));
    http_response_code(500);
    echo 'Não foi possível carregar o produto.';
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);

if (!mysqli_stmt_execute($stmt)) {
    error_log('[ecommerce compra] Falha ao executar consulta do produto id=' . $id . ': ' . mysqli_stmt_error($stmt));
    http_response_code(500);
    echo 'Não foi possível carregar o produto.';
    exit;
}

$resultado = mysqli_stmt_get_result($stmt);
if ($resultado === false) {
    error_log('[ecommerce compra] Falha ao obter resultado do produto id=' . $id . ': ' . mysqli_stmt_error($stmt));
    http_response_code(500);
    echo 'Não foi possível carregar o produto.';
    exit;
}

$produto = mysqli_fetch_assoc($resultado);

if (!$produto) {
    http_response_code(404);
    echo 'Produto não encontrado.';
    exit;
}

$imagemProduto = imagemProdutoUrl((string) ($produto['Imagem'] ?? ''));

$id = (int) ($_GET['id'] ?? 0);
$produto = null;
$produtoFavoritado = false;
$mensagemComentario = '';
$tipoMensagemComentario = 'danger';

function comentarioTemPalavraObscena(string $texto): bool
{
    $textoNormalizado = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    $textoNormalizado = strtolower($textoNormalizado !== false ? $textoNormalizado : $texto);
    $textoNormalizado = strtr($textoNormalizado, [
        '0' => 'o',
        '1' => 'i',
        '3' => 'e',
        '4' => 'a',
        '5' => 's',
        '7' => 't',
        '@' => 'a',
        '$' => 's',
    ]);
    $textoNormalizado = preg_replace('/[^a-z0-9]+/', ' ', $textoNormalizado);

    $palavrasObscenas = [
        'bosta',
        'bostas',
        'buceta',
        'bucetas',
        'cacete',
        'cacetes',
        'caralho',
        'caralhos',
        'cuzao',
        'cuzaos',
        'foda',
        'fodas',
        'foder',
        'merda',
        'merdas',
        'piranha',
        'piranhas',
        'porra',
        'porras',
        'puta',
        'putas',
        'puto',
        'putos',
        'vadia',
        'vadias',
        'viado',
        'viados',
        'nigga',
        'niggas',
        'nigger',
        'niggers',
        'crioulo',
        'crioulos',
        'vagabundo',
        'vagabundos',
        'vagabunda',
        'vagabundas',
        'merdinha',
        'merdinhas',
        'estuprado',
        'estuprados',
        'estuprada',
        'estupradas',
        'retardado',
        'retardada',
        'retardados',
        'retardadas',
    ];

    foreach ($palavrasObscenas as $palavra) {
        if (preg_match('/(?:^| )' . preg_quote($palavra, '/') . '(?:$| )/', $textoNormalizado)) {
            return true;
        }
    }

    return false;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM Produto WHERE id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $produto = mysqli_fetch_assoc($resultado);
    }
}

if (!$produto) {
    echo 'Produto não encontrado.';
    exit;
}

if (!empty($_SESSION['usuario_id'])) {
    $usuarioId = (int) $_SESSION['usuario_id'];
    $stmtFavorito = mysqli_prepare($conn, 'SELECT id FROM Produto_desejo WHERE id_usuario = ? AND id_produto = ? LIMIT 1');
    if ($stmtFavorito) {
        mysqli_stmt_bind_param($stmtFavorito, 'ii', $usuarioId, $id);
        mysqli_stmt_execute($stmtFavorito);
        $produtoFavoritado = mysqli_num_rows(mysqli_stmt_get_result($stmtFavorito)) > 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar_comentario'])) {
    if (empty($_SESSION['usuario_id'])) {
        $mensagemComentario = 'Faça login para excluir um comentário.';
    } else {
        $idComentario = (int) $_POST['apagar_comentario'];
        $usuarioId = (int) $_SESSION['usuario_id'];
        $ehAdmin = ($_SESSION['usuario_tipo'] ?? '') === 'admin';

        if ($ehAdmin) {
            $stmtApagar = mysqli_prepare($conn, 'DELETE FROM Comentario WHERE id = ? AND id_produto = ?');
            if ($stmtApagar) {
                mysqli_stmt_bind_param($stmtApagar, 'ii', $idComentario, $id);
            }
        } else {
            $stmtApagar = mysqli_prepare($conn, 'DELETE FROM Comentario WHERE id = ? AND id_produto = ? AND id_usuario = ?');
            if ($stmtApagar) {
                mysqli_stmt_bind_param($stmtApagar, 'iii', $idComentario, $id, $usuarioId);
            }
        }

        if ($stmtApagar && mysqli_stmt_execute($stmtApagar) && mysqli_stmt_affected_rows($stmtApagar) > 0) {
            $mensagemComentario = 'Comentário excluído com sucesso.';
            $tipoMensagemComentario = 'success';
        } else {
            $mensagemComentario = 'Não foi possível excluir o comentário.';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    if (empty($_SESSION['usuario_id'])) {
        $mensagemComentario = 'Faça login para comentar neste produto.';
    } else {
        $textoComentario = trim($_POST['comentario']);

        if ($textoComentario === '') {
            $mensagemComentario = 'Escreva um comentário antes de enviar.';
        } elseif (strlen($textoComentario) > 1000) {
            $mensagemComentario = 'O comentário deve ter no máximo 1000 caracteres.';
        } elseif (comentarioTemPalavraObscena($textoComentario)) {
            $mensagemComentario = 'O comentário contém palavras que não são permitidas.';
        } else {
            $stmtComentario = mysqli_prepare($conn, 'INSERT INTO Comentario (id_produto, id_usuario, Texto) VALUES (?, ?, ?)');

            if (!$stmtComentario) {
                $mensagemComentario = 'Não foi possível preparar o comentário.';
            } else {
                $usuarioId = (int) $_SESSION['usuario_id'];
                mysqli_stmt_bind_param($stmtComentario, 'iis', $id, $usuarioId, $textoComentario);

                if (mysqli_stmt_execute($stmtComentario)) {
                    $mensagemComentario = 'Comentário publicado com sucesso.';
                    $tipoMensagemComentario = 'success';
                } else {
                    $mensagemComentario = 'Não foi possível publicar o comentário.';
                }
            }
        }
    }
}

$comentarios = [];
$stmtComentarios = mysqli_prepare(
    $conn,
    'SELECT c.id, c.id_usuario, c.Texto, c.data_criacao, u.Nome, u.Foto_perfil
     FROM Comentario c
     INNER JOIN Usuario u ON u.id = c.id_usuario
     WHERE c.id_produto = ?
     ORDER BY c.data_criacao DESC'
);

if ($stmtComentarios) {
    mysqli_stmt_bind_param($stmtComentarios, 'i', $id);
    mysqli_stmt_execute($stmtComentarios);
    $resultadoComentarios = mysqli_stmt_get_result($stmtComentarios);

    while ($comentario = mysqli_fetch_assoc($resultadoComentarios)) {
        $comentarios[] = $comentario;
    }
}

?>

<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <title>Compra</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= BASE_URL ?>telas/compra/tela_compra.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <?php require_once __DIR__ . '/../../include/navbar.php'; ?>

    <main>
        <div class="container py-4">

            <div class="produto-compra border">

                <div class="produto-imagem">

                    <div class="imagem-container">

                        <img
                            src="<?= htmlspecialchars($imagemProduto, ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars((string) ($produto['Nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                </div>

                <div class="produto-informacoes">

                    <h1>
                        <?= htmlspecialchars((string) ($produto['Nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        <a href="<?= BASE_URL ?>telas/lista_desejos/<?= $produtoFavoritado ? 'remover.php' : 'adicionar.php' ?>?id=<?= (int) $produto['id'] ?>" class="icone-desejo" title="<?= $produtoFavoritado ? 'Remover da lista de desejos' : 'Adicionar à lista de desejos' ?>">
                            <i class="bi <?= $produtoFavoritado ? 'bi-heart-fill' : 'bi-heart' ?> fs-2"></i>
                        </a>
                    </h1>

                    <p class="descricao">
                        <?= htmlspecialchars((string) ($produto['Descricao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="avaliacao">

                        <span class="estrelas">
                            ★★★★★
                        </span>

                        <span class="avaliacao-texto">
                            4.8 (67 avaliações)
                        </span>

                    </div>

                    <hr>

                    <div class="preco-area">

                        <span class="preco">
                            R$ <?= number_format((float) ($produto['Preco'] ?? 0), 2, ',', '.') ?>
                        </span>

                    </div>

                    <div class="pagamento">

                        <strong>
                            ou em até 12x no cartão
                        </strong>

                        <p>
                            Consulte as condições de parcelamento
                        </p>

                    </div>

                    <div class="botoes-compra">

                        <a href="<?= BASE_URL ?>carrinho/adicionar.php?id=<?= (int) $produto['id'] ?>" class="btn btn-success" role="button">Comprar agora</a>

                        <a href="<?= BASE_URL ?>carrinho/adicionar.php?id=<?= (int) $produto['id'] ?>" class="btn btn-carrinho" role="button"><img src="<?= BASE_URL ?>assets/img/carrinho.png" alt="" style="width: 50px; height: 50px;" class="mb-2">Adicionar ao carrinho</a>

                    </div>

                </div>

            </div>

            <section class="descricao-produto border">

                <h2>
                    Descrição do produto
                </h2>

                <p>
                    <?= htmlspecialchars((string) ($produto['Descricao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                </p>

            </section>

            <section class="comentarios-produto border" id="comentarios">
                <h2>Comentários</h2>

                <?php if ($mensagemComentario): ?>
                    <div class="alert alert-<?= htmlspecialchars($tipoMensagemComentario, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($mensagemComentario, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <form method="POST" action="?id=<?= (int) $produto['id'] ?>#comentarios" class="mb-4">
                        <label for="comentario" class="form-label">Deixe seu comentário</label>
                        <textarea id="comentario" name="comentario" class="form-control" rows="4" maxlength="1000" required></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Publicar comentário</button>
                    </form>
                <?php else: ?>
                    <p>
                        <a href="<?= BASE_URL ?>tela_login/tela_login.php">Entre na sua conta</a>
                        para publicar um comentário.
                    </p>
                <?php endif; ?>

                <?php if ($comentarios): ?>
                    <?php foreach ($comentarios as $comentario): ?>
                        <article class="comentario-item">
                            <div class="d-flex justify-content-between gap-3">
                                <?php
                                $fotoComentario = trim($comentario['Foto_perfil'] ?? '');
                                $fotoComentario = $fotoComentario !== ''
                                    ? BASE_URL . ltrim($fotoComentario, '/')
                                    : BASE_URL . 'assets/img/perfil.png';
                                ?>
                                <div class="comentario-autor">
                                    <img
                                        src="<?= htmlspecialchars($fotoComentario, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="Foto de <?= htmlspecialchars($comentario['Nome'], ENT_QUOTES, 'UTF-8') ?>"
                                        class="comentario-avatar">
                                    <strong><?= htmlspecialchars($comentario['Nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </div>
                                <small><?= date('d/m/Y H:i', strtotime($comentario['data_criacao'])) ?></small>
                            </div>
                            <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($comentario['Texto'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <?php if (!empty($_SESSION['usuario_id']) && ((int) $_SESSION['usuario_id'] === (int) $comentario['id_usuario'] || ($_SESSION['usuario_tipo'] ?? '') === 'admin')): ?>
                                <form method="POST" action="?id=<?= (int) $produto['id'] ?>#comentarios" class="mt-2">
                                    <button type="submit" name="apagar_comentario" value="<?= (int) $comentario['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este comentário?');">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-secondary mb-0">Ainda não há comentários para este produto.</p>
                <?php endif; ?>
            </section>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../include/footer.php'; ?>

    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>