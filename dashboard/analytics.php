<?php
require_once '../config/app.php';
require_once '../classes/User.php';
require_once '../classes/Inventory.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

$currentUser = $user->getCurrentUser();
$inventory = new Inventory();

// 1. Distribuição por status
$statusData = $inventory->getStatusDistribution();
// 2. Quantidade por modelo (top 10)
$modelData = $inventory->getTopModels(10);
// 3. Evolução por ano (linha)
$yearData = $inventory->getYearlyEvolution();
// 4. Quantidade em estoque por modelo
$stockByModel = $inventory->getStockByModel();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Analítico - Estoque de APs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-container {
            position: relative;
            height: 350px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0">
            <div class="text-white p-3">
                <h5><i class="fas fa-wifi"></i> Estoque Ruckus</h5>
                <hr class="text-white">
                <div class="mb-3">
                    <small>Usuário logado:</small><br>
                    <strong><?= htmlspecialchars($currentUser['name']) ?></strong>
                </div>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">
                            <i class="fas fa-list"></i> Listar Itens
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="add_item.php">
                            <i class="fas fa-plus"></i> Novo Item
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="import_google_sheets.php">
                            <i class="fab fa-google"></i> Google Sheets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="analytics.php">
                            <i class="fas fa-chart-bar"></i> Painel Analítico
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-white" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-10 main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-bar"></i> Painel Analítico do Estoque de APs</h2>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-chart-pie"></i> Distribuição por Status
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-chart-bar"></i> Top 10 Modelos
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="modelChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-chart-line"></i> Evolução por Ano
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="yearChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-box"></i> APs em Estoque por Modelo
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 400px;">
                                <canvas id="stockByModelChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Dados do PHP para JS
const statusLabels = <?= json_encode(array_keys($statusData)) ?>;
const statusValues = <?= json_encode(array_values($statusData)) ?>;
const modelLabels = <?= json_encode(array_keys($modelData)) ?>;
const modelValues = <?= json_encode(array_values($modelData)) ?>;
const yearLabels = <?= json_encode(array_keys($yearData)) ?>;
const yearValues = <?= json_encode(array_values($yearData)) ?>;
const stockByModelLabels = <?= json_encode(array_keys($stockByModel)) ?>;
const stockByModelValues = <?= json_encode(array_values($stockByModel)) ?>;

// Gráfico de Pizza - Status
new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: [
                '#28a745', '#17a2b8', '#dc3545', '#fd7e14', '#6c757d', '#ffc107', '#e83e8c', '#6f42c1', '#007bff'
            ],
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Gráfico de Barras - Modelos
new Chart(document.getElementById('modelChart'), {
    type: 'bar',
    data: {
        labels: modelLabels,
        datasets: [{
            label: 'Quantidade',
            data: modelValues,
            backgroundColor: '#764ba2',
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { beginAtZero: true }
        }
    }
});

// Gráfico de Linha - Evolução por Ano
new Chart(document.getElementById('yearChart'), {
    type: 'line',
    data: {
        labels: yearLabels,
        datasets: [{
            label: 'Inclusões',
            data: yearValues,
            fill: false,
            borderColor: '#007bff',
            backgroundColor: '#007bff',
            tension: 0.2
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Gráfico de Barras - APs em Estoque por Modelo
new Chart(document.getElementById('stockByModelChart'), {
    type: 'bar',
    data: {
        labels: stockByModelLabels,
        datasets: [{
            label: 'Em Estoque',
            data: stockByModelValues,
            backgroundColor: '#ffc107',
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { beginAtZero: true }
        }
    }
});
</script>
</body>
</html> 