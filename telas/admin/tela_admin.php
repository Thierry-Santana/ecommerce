<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../include/verificar_admin.php';
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <title>Painel Admin</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous" />
</head>

<body>
    
    <?php require_once __DIR__ . '/../../include/navbar_admin.php'; ?>

    <main class="container py-4">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Vendas totais</h6>
                        <h3>R$ 25.430</h3>
                        <small class="text-success">↑ 12% este mês</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Pedidos</h6>
                        <h3>156</h3>
                        <small class="text-success">↑ 3% este mês</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Produtos</h6>
                        <h3>120</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Clientes</h6>
                        <h3>85</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Pedidos recentes</h5>
                    <a href="<?= BASE_URL ?>telas/pedidos/listar.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nº pedido</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#1042</td>
                                <td>Maria Oliveira</td>
                                <td>30/08/2026</td>
                                <td>R$ 899,00</td>
                                <td><span class="badge bg-success">Entregue</span></td>
                            </tr>
                            <tr>
                                <td>#1041</td>
                                <td>João Pereira</td>
                                <td>29/08/2026</td>
                                <td>R$ 1.249,90</td>
                                <td><span class="badge bg-warning text-dark">Em trânsito</span></td>
                            </tr>
                            <tr>
                                <td>#1040</td>
                                <td>Fernanda Costa</td>
                                <td>28/08/2026</td>
                                <td>R$ 459,00</td>
                                <td><span class="badge bg-primary">Processando</span></td>
                            </tr>
                            <tr>
                                <td>#1039</td>
                                <td>Lucas Mendes</td>
                                <td>27/08/2026</td>
                                <td>R$ 699,90</td>
                                <td><span class="badge bg-danger">Cancelado</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>