<?php
/**
 * View Item Details Page
 */

require_once '../config/app.php';
require_once '../classes/User.php';
require_once '../classes/Inventory.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

$currentUser = $user->getCurrentUser();
$inventory = new Inventory();

// Get item ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php?message=' . urlencode('ID do item não fornecido'));
    exit;
}

// Get item details
$result = $inventory->getById($id);

if (!$result['success']) {
    header('Location: index.php?message=' . urlencode($result['message']));
    exit;
}

$item = $result['item'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha do AP - Sistema de Estoque Ruckus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .info-label {
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 1.15rem;
            font-weight: 500;
            color: #fff;
        }
        .status-badge {
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 1.1rem;
            display: inline-block;
        }
        .status-instalado { background-color: #28a745; color: #fff; }
        .status-estoque { background-color: #17a2b8; color: #fff; }
        .status-desaparecido { background-color: #dc3545; color: #fff; }
        .status-queimado { background-color: #fd7e14; color: #fff; }
        .status-descarte { background-color: #6c757d; color: #fff; }
        .status-verificar { background-color: #ffc107; color: #212529; }
        .status-flagged { background-color: #e83e8c; color: #fff; }
        .status-manutenção { background-color: #6f42c1; color: #fff; }
        .mac-address {
            font-family: 'Courier New', monospace;
            background-color: #fff;
            color: #333;
            padding: 10px 16px;
            border-radius: 7px;
            border: 1px solid #e0e0e0;
            font-size: 1.1rem;
            margin-bottom: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .qr-code {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .info-card .row > div { margin-bottom: 18px; }
        .info-card h4 { font-size: 2rem; font-weight: 700; margin-bottom: 18px; }
        .info-card .mb-3 { margin-bottom: 14px !important; }
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
                            <a class="nav-link text-white" href="analytics.php">
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
                    <div class="d-flex align-items-center">
                        <a href="index.php" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <h2 class="mb-0"><i class="fas fa-id-card"></i> Ficha do Access Point</h2>
                    </div>
                    <div>
                        <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="delete_item.php?id=<?= $item['id'] ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir este item?')">
                            <i class="fas fa-trash"></i> Excluir
                        </a>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Main Information Card -->
                    <div class="col-md-8">
                        <div class="card info-card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="text-white mb-4">
                                            <i class="fas fa-wifi"></i> 
                                            <?= !empty($item['apname']) ? htmlspecialchars($item['apname']) : 'AP Sem Nome' ?>
                                        </h4>
                                        <div class="mb-3">
                                            <div class="info-label">MAC Address</div>
                                            <div class="mac-address info-value">
                                                <?= !empty($item['apmac']) ? htmlspecialchars($item['apmac']) : '<em class="text-muted">Não informado</em>' ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="info-label">Modelo</div>
                                            <div class="info-value">
                                                <?= !empty($item['model']) ? htmlspecialchars($item['model']) : '<em class="text-muted">Não informado</em>' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="info-label">Status</div>
                                            <div class="status-badge status-<?= strtolower($item['status']) ?>">
                                                <i class="fas fa-circle"></i> <?= htmlspecialchars($item['status']) ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="info-label">Serial</div>
                                            <div class="info-value">
                                                <?= !empty($item['serial']) ? htmlspecialchars($item['serial']) : '<em class="text-muted">Não informado</em>' ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="info-label">Data de Inclusão</div>
                                            <div class="info-value">
                                                <?= !empty($item['inclusao']) && $item['inclusao'] !== '0000-00-00' ? date('d/m/Y', strtotime($item['inclusao'])) : '<em class="text-muted">Não informado</em>' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location and Observations -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Localização e Observações</h5>
                            </div>
                            <div class="card-body">
                                <!-- DEBUG: Exibir valores reais de location e obs em linhas separadas -->
                                <div style="background:#fffbe6;color:#b94a48;padding:10px;border:1px solid #fbeed5;">
                                    Location: <?= var_export($item['location'], true) ?><br>
                                    Obs: <?= var_export($item['obs'], true) ?>
                                </div>
                                <div class="mb-4">
                                    <div class="info-label">Localização</div>
                                    <div class="info-value">
                                        <?= isset($item['location']) && trim($item['location']) !== '' ? htmlspecialchars($item['location']) : '<em class="text-muted">Localização não informada</em>' ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="info-label">Observações</div>
                                    <div class="info-value">
                                        <?= isset($item['obs']) && trim($item['obs']) !== '' ? htmlspecialchars($item['obs']) : '<em class="text-muted">Nenhuma observação</em>' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar Information -->
                    <div class="col-md-4">
                        <!-- QR Code Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-qrcode"></i> Código QR</h5>
                            </div>
                            <div class="card-body">
                                <div class="qr-code">
                                    <div class="mb-3">
                                        <i class="fas fa-qrcode fa-4x text-muted"></i>
                                    </div>
                                    <small class="text-muted">
                                        MAC: <?= !empty($item['apmac']) ? htmlspecialchars($item['apmac']) : 'N/A' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar AP
                                    </a>
                                    <button class="btn btn-info btn-sm" onclick="copyToClipboard('<?= $item['apmac'] ?>')">
                                        <i class="fas fa-copy"></i> Copiar MAC
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="printFicha()">
                                        <i class="fas fa-print"></i> Imprimir Ficha
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text) {
            if (text && text !== 'Não informado') {
                navigator.clipboard.writeText(text).then(function() {
                    alert('MAC Address copiado para a área de transferência!');
                });
            } else {
                alert('MAC Address não disponível para cópia.');
            }
        }
        
        function printFicha() {
            window.print();
        }
    </script>
</body>
</html> 