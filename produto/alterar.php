<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';
require_once __DIR__ . '/../include/produto.php';

$id = (int) ($_GET['id'] ?? 0);
$mensagem = '';
$mensagemTipo = 'danger';

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

$stmtProduto = mysqli_prepare($conn, 'SELECT * FROM Produto WHERE id = ? LIMIT 1');

if (!$stmtProduto) {
    die('Erro ao preparar consulta do produto: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtProduto, 'i', $id);
mysqli_stmt_execute($stmtProduto);

$produto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtProduto));

if (!$produto) {
    header('Location: listar.php');
    exit;
}

$vendedores = mysqli_query(
    $conn,
    'SELECT id, Nome FROM Vendedor ORDER BY Nome ASC'
);

if ($vendedores === false) {
    $mensagem = 'Erro ao carregar vendedores: ' . mysqli_error($conn);
    $vendedores = null;
}

$categorias = mysqli_query(
    $conn,
    'SELECT id, Nome FROM Categoria ORDER BY Nome ASC'
);

if ($categorias === false) {
    $mensagem = 'Erro ao carregar categorias: ' . mysqli_error($conn);
    $categorias = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idVendedor = (int) ($_POST['id_vendedor'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $precoInput = trim($_POST['preco'] ?? '');
    $freteInput = trim($_POST['frete'] ?? '');
    $estoque = (int) ($_POST['estoque'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    /*
     * A imagem que já está cadastrada no banco.
     * IMPORTANTE: aqui temos uma STRING, não o $_FILES inteiro.
     */
    $imagem = $produto['Imagem'] ?? '';

    /*
     * Verifica se uma nova imagem foi enviada.
     */
    $novaImagem = $_FILES['imagem'] ?? null;
    $validacaoNovaImagem = null;

    if ($novaImagem !== null && $novaImagem['error'] !== UPLOAD_ERR_NO_FILE) {
        $validacaoNovaImagem = validarUploadImagem($novaImagem);
    }

    if (
        $nome === '' ||
        $descricao === '' ||
        $idCategoria <= 0
    ) {
        $mensagem = 'Nome, descrição e categoria são obrigatórios.';
    } elseif (
        $precoInput === '' ||
        !is_numeric(str_replace(',', '.', $precoInput))
    ) {
        $mensagem = 'Informe um valor válido para o preço.';
    } elseif (
        $freteInput === '' ||
        !is_numeric(str_replace(',', '.', $freteInput))
    ) {
        $mensagem = 'Informe um valor válido para o frete.';
    } elseif ($estoque < 0) {
        $mensagem = 'O estoque não pode ser negativo.';
    } elseif ($validacaoNovaImagem !== null && !$validacaoNovaImagem['ok']) {
        $mensagem = $validacaoNovaImagem['mensagem'];
    } else {

        /*
         * Se uma nova imagem foi enviada, usamos somente
         * o nome do arquivo para salvar no banco.
         *
         * NÃO usamos o array $_FILES diretamente no UPDATE.
         */
        if (
            $novaImagem !== null &&
            $novaImagem['error'] === UPLOAD_ERR_OK
        ) {
            $nomeArquivo = 'produto_' . bin2hex(random_bytes(16)) . '.' . $validacaoNovaImagem['extensao'];
            $pastaDestino = prepararDiretorioProdutos();

            if ($pastaDestino === null) {
                $mensagem = 'Não foi possível preparar o diretório de imagens. Consulte o log do PHP.';
                $novaImagem = null;
            } elseif (!move_uploaded_file($novaImagem['tmp_name'], $pastaDestino . $nomeArquivo)) {
                error_log(sprintf(
                    '[ecommerce upload] Falha ao mover nova imagem: source=%s destination=%s upload_error=%s',
                    $novaImagem['tmp_name'],
                    $pastaDestino . $nomeArquivo,
                    $novaImagem['error']
                ));
                $mensagem = 'Erro ao mover a nova imagem para o diretório de produtos. Consulte o log do PHP.';
                $novaImagem = null;
            } else {
                $imagem = '/ecommerce/assets/img/' . $nomeArquivo;
            }
        }

        if ($mensagem !== '') {
            $novaImagem = null;
        }

        if ($mensagem === '') {
            $stmtVendedor = mysqli_prepare(
                $conn,
                'SELECT id FROM Vendedor WHERE id = ? LIMIT 1'
            );

        if (!$stmtVendedor) {

            $mensagem = 'Erro ao validar vendedor: ' . mysqli_error($conn);
        } else {

            mysqli_stmt_bind_param(
                $stmtVendedor,
                'i',
                $idVendedor
            );

            mysqli_stmt_execute($stmtVendedor);

            $vendedorExiste = mysqli_stmt_get_result($stmtVendedor);

            if (mysqli_num_rows($vendedorExiste) === 0) {

                $mensagem = 'O vendedor informado não existe.';
            } else {

                $stmtCategoria = mysqli_prepare(
                    $conn,
                    'SELECT id FROM Categoria WHERE id = ? LIMIT 1'
                );

                if (!$stmtCategoria) {

                    $mensagem = 'Erro ao validar categoria: ' . mysqli_error($conn);
                } else {

                    mysqli_stmt_bind_param(
                        $stmtCategoria,
                        'i',
                        $idCategoria
                    );

                    mysqli_stmt_execute($stmtCategoria);

                    $categoriaExiste = mysqli_stmt_get_result($stmtCategoria);

                    if (mysqli_num_rows($categoriaExiste) === 0) {

                        $mensagem = 'A categoria informada não existe.';
                    } else {

                        $preco = (float) str_replace(',', '.', $precoInput);
                        $frete = (float) str_replace(',', '.', $freteInput);

                        $sql = '
                            UPDATE Produto
                            SET
                                id_vendedor = ?,
                                Nome = ?,
                                Imagem = ?,
                                Preco = ?,
                                Frete = ?,
                                Estoque = ?,
                                Descricao = ?,
                                id_categoria = ?
                            WHERE id = ?
                        ';

                        $stmt = mysqli_prepare($conn, $sql);

                        if (!$stmt) {

                            $mensagem =
                                'Erro ao preparar SQL da atualização: '
                                . mysqli_error($conn);
                        } else {

                            /*
                             * Agora $imagem é uma STRING.
                             * Não estamos mais passando $_FILES['imagem'].
                             */
                            mysqli_stmt_bind_param(
                                $stmt,
                                'issddisii',
                                $idVendedor,
                                $nome,
                                $imagem,
                                $preco,
                                $frete,
                                $estoque,
                                $descricao,
                                $idCategoria,
                                $id
                            );

                            if (!mysqli_stmt_execute($stmt)) {

                                $mensagem =
                                    'Erro ao atualizar o produto: '
                                    . mysqli_stmt_error($stmt);
                            } else {

                                $mensagem =
                                    'Produto atualizado com sucesso.';

                                $mensagemTipo = 'success';

                                /*
                                 * Atualiza os dados exibidos na página
                                 * sem colocar o array $_FILES dentro
                                 * de $produto['Imagem'].
                                 */
                                $produto['id_vendedor'] = $idVendedor;
                                $produto['Nome'] = $nome;
                                $produto['Imagem'] = $imagem;
                                $produto['Preco'] = $preco;
                                $produto['Frete'] = $frete;
                                $produto['Estoque'] = $estoque;
                                $produto['Descricao'] = $descricao;
                                $produto['id_categoria'] = $idCategoria;
                            }
                        }
                    }
                }
            }
            }
        }
    }
}
?>


<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>
    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 800px;">
            <div class="card-body">
                <h2 class="mb-4">Editar produto</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="id_vendedor">Vendedor</label>
                            <select id="id_vendedor" name="id_vendedor" class="form-select" required>
                                <?php if ($vendedores && mysqli_num_rows($vendedores) > 0): ?>
                                    <?php mysqli_data_seek($vendedores, 0); ?>
                                    <?php while ($vendedor = mysqli_fetch_assoc($vendedores)): ?>
                                        <option value="<?= (int) $vendedor['id'] ?>" <?= ((int) $produto['id_vendedor'] ?? 0) === (int) $vendedor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($vendedor['Nome'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="id_categoria">Categoria</label>
                            <select id="id_categoria" name="id_categoria" class="form-select" required>
                                <?php if ($categorias && mysqli_num_rows($categorias) > 0): ?>
                                    <?php mysqli_data_seek($categorias, 0); ?>
                                    <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>
                                        <option value="<?= (int) $categoria['id'] ?>" <?= ((int) $produto['id_categoria'] ?? 0) === (int) $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['Nome'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($produto['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="imagem">Imagem</label>

                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($produto['Imagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    readonly>

                                <label class="btn btn-secondary">
                                    Escolher nova imagem
                                    <input
                                        type="file"
                                        name="imagem"
                                        accept="image/*"
                                        hidden>
                                </label>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <label class="form-label" for="preco">Preço</label>
                            <input type="text" id="preco" name="preco" class="form-control" value="<?= htmlspecialchars((string) ($produto['Preco'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="frete">Frete</label>
                            <input type="text" id="frete" name="frete" class="form-control" value="<?= htmlspecialchars((string) ($produto['Frete'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="estoque">Estoque</label>
                            <input type="number" id="estoque" name="estoque" class="form-control" value="<?= (int) ($produto['Estoque'] ?? 0) ?>" min="0" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="4" required><?= htmlspecialchars($produto['Descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Atualizar produto</button>
                        <a href="listar.php" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>