<?php
/**
 * Add New Inventory Item Page
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
$modelOptions = $inventory->getModelOptions();
$statusOptions = $inventory->getStatusOptions();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Item - Sistema de Estoque Ruckus</title>
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
                            <a class="nav-link text-white active" href="add_item.php">
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
                    <h2><i class="fas fa-plus"></i> Adicionar Novo Item</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-<?= isset($_GET['type']) && $_GET['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                        <?= htmlspecialchars($_GET['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wifi"></i> Dados do Access Point</h5>
                    </div>
                    <div class="card-body">
                        <form action="add_item_process.php" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apmac" class="form-label">
                                            <i class="fas fa-network-wired"></i> MAC Address *
                                        </label>
                                        <input type="text" class="form-control" id="apmac" name="apmac" 
                                               placeholder="00:11:22:33:44:55" pattern="[0-9A-Fa-f:]{17}">
                                        <small class="form-text text-muted">Formato: XX:XX:XX:XX:XX:XX (sem prefixo MAC)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apname" class="form-label">
                                            <i class="fas fa-tag"></i> Nome do AP *
                                        </label>
                                        <input type="text" class="form-control" id="apname" name="apname" 
                                               placeholder="AP-LOBBY-01">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="model" class="form-label">
                                            <i class="fas fa-microchip"></i> Modelo *
                                        </label>
                                        <select class="form-select" id="model" name="model">
                                            <option value="">Selecione o modelo</option>
                                            <?php if (!empty($modelOptions)): ?>
                                                <?php foreach ($modelOptions as $model): ?>
                                                    <option value="<?= htmlspecialchars($model) ?>"><?= htmlspecialchars($model) ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <!-- Fallback options if database is empty -->
                                                <option value="R750">R750</option>
                                                <option value="R650">R650</option>
                                                <option value="R550">R550</option>
                                                <option value="R350">R350</option>
                                                <option value="T350D">T350D</option>
                                                <option value="T350S">T350S</option>
                                                <option value="H510">H510</option>
                                                <option value="P300">P300</option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="form-text text-muted">Modelos cadastrados no sistema</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="serial" class="form-label">
                                            <i class="fas fa-barcode"></i> Número Serial *
                                        </label>
                                        <input type="text" class="form-control" id="serial" name="serial" 
                                               placeholder="RK7501234567">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-circle"></i> Status
                                        </label>
                                        <select class="form-select" id="status" name="status">
                                            <?php if (!empty($statusOptions)): ?>
                                                <?php foreach ($statusOptions as $status): ?>
                                                    <option value="<?= htmlspecialchars($status) ?>" <?= $status === 'Instalado' ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($status) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <!-- Fallback options if database is empty -->
                                                <option value="Instalado" selected>Instalado</option>
                                                <option value="Estoque">Estoque</option>
                                                <option value="Desaparecido">Desaparecido</option>
                                                <option value="Queimado">Queimado</option>
                                                <option value="Descarte">Descarte</option>
                                                <option value="Verificar">Verificar</option>
                                                <option value="Flagged">Flagged</option>
                                                <option value="Manutenção">Manutenção</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="inclusao" class="form-label">
                                            <i class="fas fa-calendar"></i> Data de Inclusão *
                                        </label>
                                        <input type="date" class="form-control" id="inclusao" name="inclusao" 
                                               value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Localização *
                                </label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="Prédio A - 3º Andar - Sala 301">
                            </div>
                            
                            <div class="mb-3">
                                <label for="obs" class="form-label">
                                    <i class="fas fa-sticky-note"></i> Observações
                                </label>
                                <textarea class="form-control" id="obs" name="obs" rows="3" 
                                          placeholder="Informações adicionais sobre o AP..."></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format MAC address input
        document.getElementById('apmac').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            let formattedValue = value.match(/.{1,2}/g)?.join(':') || value;
            if (formattedValue.length > 17) {
                formattedValue = formattedValue.substring(0, 17);
            }
            e.target.value = formattedValue.toUpperCase();
        });
    </script>
</body>
</html>
