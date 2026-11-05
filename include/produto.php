<?php

require_once __DIR__ . '/../config.php';

function bytesConfigurados(string $valor): int
{
    $valor = trim($valor);
    $unidade = strtolower(substr($valor, -1));
    $numero = (float) $valor;

    if ($unidade === 'g') {
        $numero *= 1024 * 1024 * 1024;
    } elseif ($unidade === 'm') {
        $numero *= 1024 * 1024;
    } elseif ($unidade === 'k') {
        $numero *= 1024;
    }

    return (int) $numero;
}

function validarUploadImagem(?array $arquivo): array
{
    if (!$arquivo || !isset($arquivo['error'])) {
        $limitePost = bytesConfigurados((string) ini_get('post_max_size'));
        $tamanhoRequisicao = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

        if ($limitePost > 0 && $tamanhoRequisicao > $limitePost) {
            return ['ok' => false, 'mensagem' => 'O envio excede o limite post_max_size do PHP.'];
        }

        return ['ok' => false, 'mensagem' => 'Nenhum arquivo de imagem foi enviado.'];
    }

    $mensagensErro = [
        UPLOAD_ERR_INI_SIZE => 'O arquivo excede o limite upload_max_filesize do PHP.',
        UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o limite definido pelo formulário.',
        UPLOAD_ERR_PARTIAL => 'O upload da imagem foi interrompido antes de terminar.',
        UPLOAD_ERR_NO_FILE => 'Nenhum arquivo de imagem foi enviado.',
        UPLOAD_ERR_NO_TMP_DIR => 'A pasta temporária de upload não está disponível.',
        UPLOAD_ERR_CANT_WRITE => 'O PHP não conseguiu gravar o arquivo temporário.',
        UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP interrompeu o upload.',
    ];

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return [
            'ok' => false,
            'mensagem' => $mensagensErro[$arquivo['error']] ?? 'O upload da imagem falhou.',
        ];
    }

    if (empty($arquivo['tmp_name']) || !is_uploaded_file($arquivo['tmp_name'])) {
        return ['ok' => false, 'mensagem' => 'O arquivo temporário do upload é inválido.'];
    }

    $limiteUpload = bytesConfigurados((string) ini_get('upload_max_filesize'));
    if ($limiteUpload > 0 && (int) $arquivo['size'] > $limiteUpload) {
        return ['ok' => false, 'mensagem' => 'O arquivo excede o limite upload_max_filesize do PHP.'];
    }

    if ((int) $arquivo['size'] <= 0) {
        return ['ok' => false, 'mensagem' => 'O arquivo de imagem está vazio.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);
    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if ($mime === false || !isset($tiposPermitidos[$mime])) {
        return ['ok' => false, 'mensagem' => 'Formato não permitido. Use JPG, JPEG, PNG ou WEBP.'];
    }

    $dimensoes = @getimagesize($arquivo['tmp_name']);
    if ($dimensoes === false || empty($dimensoes['mime']) || !isset($tiposPermitidos[$dimensoes['mime']])) {
        return ['ok' => false, 'mensagem' => 'O arquivo não é uma imagem válida.'];
    }

    return [
        'ok' => true,
        'extensao' => $tiposPermitidos[$mime],
        'mime' => $mime,
    ];
}

function prepararDiretorioProdutos(): ?string
{
    $diretorio = __DIR__ . '/../assets/img/';

    if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
        error_log(sprintf(
            '[ecommerce upload] Não foi possível criar o diretório: path=%s exists=%s writable=%s upload_error=%s',
            $diretorio,
            is_dir($diretorio) ? 'yes' : 'no',
            is_writable($diretorio) ? 'yes' : 'no',
            $_FILES['imagem']['error'] ?? 'n/a'
        ));
        return null;
    }

    if (!is_writable($diretorio)) {
        error_log(sprintf(
            '[ecommerce upload] Diretório sem permissão de escrita: path=%s realpath=%s exists=%s writable=%s mode=%o upload_error=%s tmp_name=%s',
            $diretorio,
            realpath($diretorio) ?: 'unresolved',
            is_dir($diretorio) ? 'yes' : 'no',
            is_writable($diretorio) ? 'yes' : 'no',
            file_exists($diretorio) ? (fileperms($diretorio) & 0777) : 0,
            $_FILES['imagem']['error'] ?? 'n/a',
            $_FILES['imagem']['tmp_name'] ?? 'n/a'
        ));
        return null;
    }

    return $diretorio;
}

function buscarProdutos(mysqli $conn, ?int $idCategoria = null): array
{
    if ($idCategoria === null) {
        $stmt = mysqli_prepare($conn, 'SELECT id, Nome, Imagem, Preco, Frete, Estoque, Descricao, id_categoria FROM Produto ORDER BY id DESC');
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, Nome, Imagem, Preco, Frete, Estoque, Descricao, id_categoria FROM Produto WHERE id_categoria = ? ORDER BY id DESC');
    }

    if (!$stmt) {
        return [];
    }

    if ($idCategoria !== null) {
        mysqli_stmt_bind_param($stmt, 'i', $idCategoria);
    }

    if (!mysqli_stmt_execute($stmt)) {
        return [];
    }

    $resultado = mysqli_stmt_get_result($stmt);
    $produtos = [];

    while ($produto = mysqli_fetch_assoc($resultado)) {
        $produtos[] = [
            'id' => (int) $produto['id'],
            'nome' => $produto['Nome'],
            'preco' => (float) $produto['Preco'],
            'frete' => (float) $produto['Frete'],
            'estoque' => (int) $produto['Estoque'],
            'imagem' => imagemProdutoUrl($produto['Imagem']),
            'descricao' => $produto['Descricao'],
            'id_categoria' => (int) $produto['id_categoria'],
        ];
    }

    return $produtos;
}

function imagemProdutoUrl(string $imagem): string
{
    if (preg_match('#^https?://#i', $imagem)) {
        return $imagem;
    }

    return BASE_URL . ltrim(str_replace('/ecommerce/', '', $imagem), '/');
}

function renderizarCardProduto(array $produto, string $classes = ''): string
{
    $nome = htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8');
    $descricao = htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8');
    $imagem = htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8');
    $preco = number_format($produto['preco'], 2, ',', '.');
    $id = (int) $produto['id'];

    return '<div class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">
        <div class="card position-relative h-100">
            <img src="' . $imagem . '" class="card-img-top" alt="' . $nome . '">
            <div class="card-body">
                <h5 class="card-title">' . $nome . '</h5>
                <p class="card-text">' . $descricao . '</p>
                <h4 class="text-success fw-bold">R$ ' . $preco . '</h4>
                <a href="' . BASE_URL . 'telas/compra/tela_compra.php?id=' . $id . '" class="stretched-link"></a>
            </div>
        </div>
    </div>';
}

if (isset($conn) && $conn instanceof mysqli) {
    $produtos = buscarProdutos($conn);
}
