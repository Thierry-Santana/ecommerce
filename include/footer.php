<?php require_once __DIR__ . '/../config.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = !empty($_SESSION['usuario_id']);
?>


<footer class="bg-body-tertiary text-white p-3 mt-4">
    <div class="container py-4">

        <div class="row">

            <div class="col">
                <h5>E-Commerce</h5>
                <p>Melhor loja de eletronicos do Brasil.</p>
            </div>

            <div class="col">
                <h5>Links</h5>

                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>index.php" class="text-white">Inicio</a></li>
                    <li><a href="" class="text-white">Produtos</a></li>
                    <?php if ($usuarioLogado): ?>
                        <li><a href="<?= BASE_URL ?>usuario/tela_usuario.php" class="text-white">Pagina do usuario</a></li>
                    <?php else: ?>
                    <li><a href="<?= BASE_URL ?>tela_login/tela_login.php" class="text-white">Login</a></li>
                    <li><a href="<?= BASE_URL ?>tela_cadastro/tela_cadastro.php" class="text-white">Cadastro</a></li>
                    <?php endif ?>
                </ul>
            </div>

            <div class="col">
                <h5>Contatos</h5>
                <p>contato@ecommerce.com</p>
                <p>(11) 99999-9999</p>
            </div>
        </div>

        <hr>
        <p class="text-center">© 2026 E-Commerce - Todos os direitos reservados.</p>
    </div>
</footer>