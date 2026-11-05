<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/verificar_admin.php';
require_once __DIR__ . '/../include/produto.php';

$mensagem = '';
$mensagemTipo = 'danger';

$categorias = mysqli_query($conn, 'SELECT id, Nome FROM Categoria ORDER BY Nome ASC');
if ($categorias === false) {
    $mensagem = 'Erro ao carregar categorias: ' . mysqli_error($conn);
    $categorias = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmtLoja = mysqli_prepare($conn, 'SELECT id FROM Vendedor ORDER BY id ASC LIMIT 1');
    mysqli_stmt_execute($stmtLoja);
    $resultadoLoja = mysqli_stmt_get_result($stmtLoja);
    $loja = mysqli_fetch_assoc($resultadoLoja);

    $idVendedor = (int) ($loja['id'] ?? 1);
    $nome = trim($_POST['nome'] ?? '');
    $imagem = $_FILES['imagem'] ?? null;
    $precoInput = trim($_POST['preco'] ?? '');
    $freteInput = trim($_POST['frete'] ?? '');
    $estoque = (int) ($_POST['estoque'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    $validacaoImagem = validarUploadImagem($imagem);

    if ($nome === '' || $descricao === '' || $idCategoria <= 0) {
        $mensagem = 'Nome, descrição e categoria são obrigatórios.';
    } elseif (!$validacaoImagem['ok']) {
        $mensagem = $validacaoImagem['mensagem'];
    } elseif (!is_numeric($precoInput) && !preg_match('/^\d+(?:[\.,]\d{1,2})?$/', $precoInput)) {
        $mensagem = 'Informe um valor válido para o preço.';
    } elseif (!is_numeric($freteInput) && !preg_match('/^\d+(?:[\.,]\d{1,2})?$/', $freteInput)) {
        $mensagem = 'Informe um valor válido para o frete.';
    } elseif ($estoque < 0) {
        $mensagem = 'O estoque não pode ser negativo.';
    } else {
        $nomeArquivo = 'produto_' . bin2hex(random_bytes(16)) . '.' . $validacaoImagem['extensao'];

        $pastaDestino = prepararDiretorioProdutos();

        if ($pastaDestino === null) {
            $mensagem = 'Não foi possível preparar o diretório de imagens. Consulte o log do PHP.';
        } elseif (!move_uploaded_file($imagem['tmp_name'], $pastaDestino . $nomeArquivo)) {
            error_log(sprintf(
                '[ecommerce upload] Falha ao mover imagem: source=%s destination=%s upload_error=%s',
                $imagem['tmp_name'],
                $pastaDestino . $nomeArquivo,
                $imagem['error']
            ));
            $mensagem = 'Erro ao mover a imagem para o diretório de produtos. Consulte o log do PHP.';
        } else {
            $caminhoBanco = '/ecommerce/assets/img/' . $nomeArquivo;

                $stmtVendedor = mysqli_prepare($conn, 'SELECT id FROM Vendedor WHERE id = ? LIMIT 1');

                if (!$stmtVendedor) {
                    $mensagem = 'Erro ao validar vendedor: ' . mysqli_error($conn);
                } else {
                    mysqli_stmt_bind_param($stmtVendedor, 'i', $idVendedor);
                    mysqli_stmt_execute($stmtVendedor);
                    $vendedorExiste = mysqli_stmt_get_result($stmtVendedor);

                    if (mysqli_num_rows($vendedorExiste) === 0) {
                        $mensagem = 'O vendedor informado não existe.';
                    } else {
                        $stmtCategoria = mysqli_prepare($conn, 'SELECT id FROM Categoria WHERE id = ? LIMIT 1');

                        if (!$stmtCategoria) {
                            $mensagem = 'Erro ao validar categoria: ' . mysqli_error($conn);
                        } else {
                            mysqli_stmt_bind_param($stmtCategoria, 'i', $idCategoria);
                            mysqli_stmt_execute($stmtCategoria);
                            $categoriaExiste = mysqli_stmt_get_result($stmtCategoria);

                            if (mysqli_num_rows($categoriaExiste) === 0) {
                                $mensagem = 'A categoria informada não existe.';
                            } else {
                                $preco = (float) str_replace(',', '.', $precoInput);
                                $frete = (float) str_replace(',', '.', $freteInput);

                                $sql = 'INSERT INTO Produto (id_vendedor, Nome, Imagem, Preco, Frete, Estoque, Descricao, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                                $stmt = mysqli_prepare($conn, $sql);

                                if (!$stmt) {
                                    $mensagem = 'Erro ao preparar SQL do cadastro: ' . mysqli_error($conn);
                                } else {
                                    mysqli_stmt_bind_param(
                                        $stmt,
                                        'issddisi',
                                        $idVendedor,
                                        $nome,
                                        $caminhoBanco,
                                        $preco,
                                        $frete,
                                        $estoque,
                                        $descricao,
                                        $idCategoria
                                    );

                                    if (!mysqli_stmt_execute($stmt)) {
                                        $mensagem = 'Erro ao inserir o produto: ' . mysqli_stmt_error($stmt);
                                    } else {
                                        $mensagem = 'Produto cadastrado com sucesso!';
                                        $mensagemTipo = 'success';
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
    <title>Cadastrar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <?php require_once __DIR__ . '/../include/navbar_admin.php'; ?>

    <div class="container py-5">
        <div class="card bg-secondary-subtle text-dark mx-auto" style="max-width: 800px;">
            <div class="card-body">
                <h2 class="mb-4">Cadastrar produto</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= htmlspecialchars($mensagemTipo, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label" for="id_categoria">Categoria</label>

                            <select id="id_categoria" name="id_categoria" class="form-select" required>
                                <option value="">Selecione</option>

                                <?php if ($categorias && mysqli_num_rows($categorias) > 0): ?>
                                    <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>
                                        <option value="<?= (int) $categoria['id'] ?>">
                                            <?= htmlspecialchars($categoria['Nome'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="nome">Nome</label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="imagem">Imagem do produto</label>

                            <input
                                type="file"
                                id="imagem"
                                name="imagem"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp"
                                required>

                            <p class="text-secondary mb-0">
                                Selecione uma imagem do produto (JPG, PNG ou WEBP).
                            </p>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="preco">Preço</label>

                            <input
                                type="text"
                                id="preco"
                                name="preco"
                                class="form-control"
                                value="0.00"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="frete">Frete</label>

                            <input
                                type="text"
                                id="frete"
                                name="frete"
                                class="form-control"
                                value="0.00"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="estoque">Estoque</label>

                            <input
                                type="number"
                                id="estoque"
                                name="estoque"
                                class="form-control"
                                value="0"
                                min="0"
                                required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="descricao">Descrição</label>

                            <textarea
                                id="descricao"
                                name="descricao"
                                class="form-control"
                                rows="4"
                                required></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            Salvar produto
                        </button>

                        <a href="listar.php" class="btn btn-outline-secondary">
                            Listar produtos
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
