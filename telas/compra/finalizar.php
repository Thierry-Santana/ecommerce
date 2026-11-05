<?php
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    $_SESSION['login_erro'] = 'Faça login para finalizar sua compra.';
    header('Location: ' . BASE_URL . 'tela_login/tela_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php');
    exit;
}

$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSessao = $_SESSION['checkout_csrf'] ?? '';
if (!is_string($tokenEnviado) || $tokenSessao === '' || !hash_equals($tokenSessao, $tokenEnviado)) {
    $_SESSION['checkout_erro'] = 'Não foi possível validar a solicitação. Atualize o carrinho e tente novamente.';
    header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    require_once __DIR__ . '/../../database/conexao.php';
} catch (Throwable $erro) {
    error_log('Falha ao conectar para finalizar compra: ' . $erro->getMessage());
    $_SESSION['checkout_erro'] = 'Não foi possível concluir a compra no momento. Tente novamente.';
    header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$transacaoAtiva = false;

try {
    mysqli_begin_transaction($conn);
    $transacaoAtiva = true;

    $stmtCarrinho = mysqli_prepare($conn, 'SELECT c.id, c.id_produto, c.Quantidade, p.Nome, p.Imagem, p.Preco, p.Frete, p.Estoque FROM Produtos_carrinho AS c INNER JOIN Produto AS p ON p.id = c.id_produto WHERE c.id_usuario = ? ORDER BY c.id FOR UPDATE');
    mysqli_stmt_bind_param($stmtCarrinho, 'i', $usuarioId);
    mysqli_stmt_execute($stmtCarrinho);
    $resultadoCarrinho = mysqli_stmt_get_result($stmtCarrinho);

    $itens = [];
    $quantidadeLinhasCarrinho = 0;
    $totalCentavos = 0;

    while ($linha = mysqli_fetch_assoc($resultadoCarrinho)) {
        $quantidadeLinhasCarrinho++;
        $idProduto = (int) $linha['id_produto'];
        $quantidade = (int) $linha['Quantidade'];

        if ($quantidade < 1) {
            throw new DomainException('Há uma quantidade inválida no carrinho. Revise os itens e tente novamente.');
        }

        if (!isset($itens[$idProduto])) {
            $itens[$idProduto] = [
                'id_produto' => $idProduto,
                'Nome' => $linha['Nome'],
                'Imagem' => $linha['Imagem'],
                'Preco' => (int) round((float) $linha['Preco'] * 100),
                'Frete' => (int) round((float) $linha['Frete'] * 100),
                'Estoque' => (int) $linha['Estoque'],
                'Quantidade' => 0,
            ];
        }

        $itens[$idProduto]['Quantidade'] += $quantidade;
    }
    mysqli_stmt_close($stmtCarrinho);

    if ($quantidadeLinhasCarrinho === 0) {
        throw new DomainException('Seu carrinho está vazio. Adicione produtos antes de finalizar a compra.');
    }

    foreach ($itens as $item) {
        if ($item['Estoque'] < $item['Quantidade']) {
            throw new DomainException('Estoque insuficiente para um ou mais produtos. Revise as quantidades no carrinho.');
        }

        // O frete é cobrado uma vez por produto, conforme o resumo atual do carrinho.
        $totalCentavos += ($item['Preco'] * $item['Quantidade']) + $item['Frete'];
    }

    $statusCompra = 'Em análise de pagamento';
    $valorTotal = number_format($totalCentavos / 100, 2, '.', '');
    $stmtCompra = mysqli_prepare($conn, 'INSERT INTO Compra (id_usuario, Status, Valor_total) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmtCompra, 'iss', $usuarioId, $statusCompra, $valorTotal);
    mysqli_stmt_execute($stmtCompra);
    $compraId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmtCompra);

    $stmtProdutoComprado = mysqli_prepare($conn, 'INSERT INTO Produtos_comprados (id_compra, id_produto, Imagem, Nome, Preco, Frete, Quantidade, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmtEstoque = mysqli_prepare($conn, 'UPDATE Produto SET Estoque = Estoque - ? WHERE id = ? AND Estoque >= ?');

    foreach ($itens as $item) {
        $idProduto = $item['id_produto'];
        $imagem = $item['Imagem'];
        $nome = $item['Nome'];
        $preco = number_format($item['Preco'] / 100, 2, '.', '');
        $frete = number_format($item['Frete'] / 100, 2, '.', '');
        $quantidade = $item['Quantidade'];

        mysqli_stmt_bind_param($stmtProdutoComprado, 'iissssii', $compraId, $idProduto, $imagem, $nome, $preco, $frete, $quantidade, $usuarioId);
        mysqli_stmt_execute($stmtProdutoComprado);

        mysqli_stmt_bind_param($stmtEstoque, 'iii', $quantidade, $idProduto, $quantidade);
        mysqli_stmt_execute($stmtEstoque);
        if (mysqli_stmt_affected_rows($stmtEstoque) !== 1) {
            throw new DomainException('O estoque de um ou mais produtos mudou. Revise o carrinho e tente novamente.');
        }
    }

    mysqli_stmt_close($stmtProdutoComprado);
    mysqli_stmt_close($stmtEstoque);

    $stmtLimparCarrinho = mysqli_prepare($conn, 'DELETE FROM Produtos_carrinho WHERE id_usuario = ?');
    mysqli_stmt_bind_param($stmtLimparCarrinho, 'i', $usuarioId);
    mysqli_stmt_execute($stmtLimparCarrinho);
    if (mysqli_stmt_affected_rows($stmtLimparCarrinho) !== $quantidadeLinhasCarrinho) {
        throw new RuntimeException('O conteúdo do carrinho mudou durante a finalização.');
    }
    mysqli_stmt_close($stmtLimparCarrinho);

    mysqli_commit($conn);
    $transacaoAtiva = false;

    $_SESSION['compra_sucesso'] = 'Compra realizada com sucesso! Seu pagamento está em análise.';
    $_SESSION['compra_id'] = $compraId;
    header('Location: ' . BASE_URL . 'telas/compra/listar.php');
    exit;
} catch (DomainException $erro) {
    if ($transacaoAtiva) {
        mysqli_rollback($conn);
    }
    $_SESSION['checkout_erro'] = $erro->getMessage();
} catch (Throwable $erro) {
    if ($transacaoAtiva) {
        mysqli_rollback($conn);
    }
    error_log('Falha ao finalizar compra: ' . $erro->getMessage());
    $_SESSION['checkout_erro'] = 'Não foi possível concluir a compra no momento. Nenhum item foi removido do carrinho.';
}

header('Location: ' . BASE_URL . 'telas/carrinho/carrinho.php');
exit;
