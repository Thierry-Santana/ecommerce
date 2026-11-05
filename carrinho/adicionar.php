<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/conexao.php';
require_once __DIR__ . '/../include/produto.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$idProduto = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$quantidade = max(1, (int) ($_GET['quantidade'] ?? $_POST['quantidade'] ?? 1));

if ($idProduto <= 0) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$produtoSelecionado = null;
foreach ($produtos as $produto) {
    if ((int) $produto['id'] === $idProduto) {
        $produtoSelecionado = $produto;
        break;
    }
}

if ($produtoSelecionado === null) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$estoqueDisponivel = (int) $produtoSelecionado['estoque'];
if ($estoqueDisponivel <= 0) {
    header('Location: ' . BASE_URL . 'index.php?erro=' . urlencode('Produto sem estoque.'));
    exit;
}

$quantidade = min($quantidade, $estoqueDisponivel);

$stmtVendedor = mysqli_prepare($conn, 'SELECT id FROM Vendedor ORDER BY id LIMIT 1');
if ($stmtVendedor) {
    mysqli_stmt_execute($stmtVendedor);
    $vendedor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtVendedor));
    $idVendedor = $vendedor['id'] ?? 0;
    if (!$idVendedor) {
        $stmtInserirVendedor = mysqli_prepare($conn, 'INSERT INTO Vendedor (Nome, Email, Senha, Foto_perfil, Produtos_cadastrados, Chat) VALUES (?, ?, ?, ?, ?, ?)');
        $senhaPadrao = password_hash('admin123', PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmtInserirVendedor, 'ssssss', $nomeVendedor, $emailVendedor, $senhaPadrao, $fotoPerfilVendedor, $produtosVendedor, $chatVendedor);
        $nomeVendedor = 'Loja Oficial';
        $emailVendedor = 'loja@ecommerce.com';
        $fotoPerfilVendedor = '';
        $produtosVendedor = '';
        $chatVendedor = '';
        mysqli_stmt_execute($stmtInserirVendedor);
        $idVendedor = (int) mysqli_insert_id($conn);
    }
}

$stmtCategoria = mysqli_prepare($conn, 'SELECT id FROM Categoria WHERE Nome = ? LIMIT 1');
$categoriaNome = 'Ofertas';
if (stripos($produtoSelecionado['nome'], 'monitor') !== false) {
    $categoriaNome = 'Monitores';
} elseif (stripos($produtoSelecionado['nome'], 'tv') !== false) {
    $categoriaNome = 'TV';
} elseif (stripos($produtoSelecionado['nome'], 'cadeira') !== false) {
    $categoriaNome = 'Cadeiras';
} elseif (stripos($produtoSelecionado['nome'], 'mouse') !== false || stripos($produtoSelecionado['nome'], 'rtx') !== false || stripos($produtoSelecionado['nome'], 'ryzen') !== false || stripos($produtoSelecionado['nome'], 'ssd') !== false || stripos($produtoSelecionado['nome'], 'alexa') !== false || stripos($produtoSelecionado['nome'], 'pc') !== false) {
    $categoriaNome = 'Periféricos';
}

mysqli_stmt_bind_param($stmtCategoria, 's', $categoriaNome);
mysqli_stmt_execute($stmtCategoria);
$categoria = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCategoria));
$idCategoria = (int) ($categoria['id'] ?? 0);

if ($idCategoria === 0) {
    $stmtInserirCategoria = mysqli_prepare($conn, 'INSERT INTO Categoria (Nome) VALUES (?)');
    mysqli_stmt_bind_param($stmtInserirCategoria, 's', $categoriaNome);
    mysqli_stmt_execute($stmtInserirCategoria);
    $idCategoria = (int) mysqli_insert_id($conn);
}

$stmtProduto = mysqli_prepare($conn, 'SELECT id FROM Produto WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmtProduto, 'i', $idProduto);
mysqli_stmt_execute($stmtProduto);
if (mysqli_num_rows(mysqli_stmt_get_result($stmtProduto)) === 0) {
    $stmtInserirProduto = mysqli_prepare($conn, 'INSERT INTO Produto (id, id_vendedor, Nome, Imagem, Preco, Frete, Estoque, Descricao, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $precoProduto = (float) $produtoSelecionado['preco'];
    $freteProduto = (float) $produtoSelecionado['frete'];
    $estoque = 10;
    $descricaoProduto = $produtoSelecionado['descricao'];
    $imagemProduto = ltrim(str_replace(BASE_URL, '', $produtoSelecionado['imagem']), '/');
    mysqli_stmt_bind_param($stmtInserirProduto, 'iissddisi', $idProduto, $idVendedor, $nomeProduto, $imagemProduto, $precoProduto, $freteProduto, $estoque, $descricaoProduto, $idCategoria);
    $nomeProduto = $produtoSelecionado['nome'];
    mysqli_stmt_execute($stmtInserirProduto);
}

$stmtCarrinho = mysqli_prepare($conn, 'SELECT id, Quantidade FROM Produtos_carrinho WHERE id_usuario = ? AND id_produto = ? LIMIT 1');
mysqli_stmt_bind_param($stmtCarrinho, 'ii', $usuarioId, $idProduto);
mysqli_stmt_execute($stmtCarrinho);
$itemCarrinho = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCarrinho));

if ($itemCarrinho) {
    $novaQuantidade = min($estoqueDisponivel, (int) $itemCarrinho['Quantidade'] + $quantidade);
    $stmtAtualizar = mysqli_prepare($conn, 'UPDATE Produtos_carrinho SET Quantidade = ?, Preco = ?, Nome = ?, Imagem = ?, Frete = ? WHERE id = ? AND id_usuario = ?');
    $precoProduto = (float) $produtoSelecionado['preco'];
    $freteProduto = (float) $produtoSelecionado['frete'];
    $nomeProduto = $produtoSelecionado['nome'];
    $imagemProduto = ltrim(str_replace(BASE_URL, '', $produtoSelecionado['imagem']), '/');
    mysqli_stmt_bind_param($stmtAtualizar, 'idssdii', $novaQuantidade, $precoProduto, $nomeProduto, $imagemProduto, $freteProduto, $itemCarrinho['id'], $usuarioId);
    mysqli_stmt_execute($stmtAtualizar);
} else {
    $stmtInserirCarrinho = mysqli_prepare($conn, 'INSERT INTO Produtos_carrinho (id_produto, Imagem, Nome, Preco, Frete, Quantidade, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $precoProduto = (float) $produtoSelecionado['preco'];
    $freteProduto = (float) $produtoSelecionado['frete'];
    $nomeProduto = $produtoSelecionado['nome'];
    $imagemProduto = ltrim(str_replace(BASE_URL, '', $produtoSelecionado['imagem']), '/');
    mysqli_stmt_bind_param($stmtInserirCarrinho, 'isssdii', $idProduto, $imagemProduto, $nomeProduto, $precoProduto, $freteProduto, $quantidade, $usuarioId);
    mysqli_stmt_execute($stmtInserirCarrinho);
}

header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php?adicionado=1');
exit;
