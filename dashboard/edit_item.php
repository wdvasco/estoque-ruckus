<?php
/**
 * Edit Inventory Item Page
 */

require_once '../classes/User.php';
require_once '../classes/Inventory.php';

// Initialize inventory instance for options
$inventory = new Inventory();
$modelOptions = $inventory->getModelOptions();
$statusOptions = $inventory->getStatusOptions();

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

// Get item ID
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($itemId <= 0) {
    header('Location: index.php?message=' . urlencode('ID do item inválido') . '&type=error');
    exit;
}

// Get item data
$inventory = new Inventory();
$result = $inventory->getById($itemId);

if (!$result['success']) {
    header('Location: index.php?message=' . urlencode($result['message']) . '&type=error');
    exit;
}

$item = $result['item'];
$currentUser = $user->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Item - Sistema de Estoque Ruckus</title>
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
                            <a class="nav-link text-white" href="add_item.php">
                                <i class="fas fa-plus"></i> Novo Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="import.php">
                                <i class="fas fa-upload"></i> Importar Excel
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
                    <h2><i class="fas fa-edit"></i> Editar Item</h2>
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
                        <form action="edit_item_process.php" method="POST">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apmac" class="form-label">
                                            <i class="fas fa-network-wired"></i> MAC Address *
                                        </label>
                                        <input type="text" class="form-control" id="apmac" name="apmac" 
                                               value="<?= preg_replace('/^MAC-?/i', '', htmlspecialchars($item['apmac'])) ?>"
                                               pattern="[0-9A-Fa-f:]{17}" required>
                                        <small class="form-text text-muted">Formato: XX:XX:XX:XX:XX:XX (sem prefixo MAC)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apname" class="form-label">
                                            <i class="fas fa-tag"></i> Nome do AP *
                                        </label>
                                        <input type="text" class="form-control" id="apname" name="apname" 
                                               value="<?= htmlspecialchars($item['apname']) ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="model" class="form-label">
                                            <i class="fas fa-microchip"></i> Modelo *
                                        </label>
                                        <select class="form-select" id="model" name="model" required>
                                            <option value="">Selecione o modelo</option>
                                            <?php foreach ($modelOptions as $model): ?>
                                                <option value="<?php echo htmlspecialchars($model); ?>" <?= $item['model'] === $model ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars($model); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="serial" class="form-label">
                                            <i class="fas fa-barcode"></i> Número Serial *
                                        </label>
                                        <input type="text" class="form-control" id="serial" name="serial" 
                                               value="<?= htmlspecialchars($item['serial']) ?>" required>
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
                                            <?php foreach ($statusOptions as $status): ?>
                                                <option value="<?php echo htmlspecialchars($status); ?>" <?= $item['status'] === $status ? 'selected' : '' ?>>
                                                    <?php 
                                                    // Translate status to Portuguese
                                                    $statusTranslations = [
                                                        'Desaparecido' => 'Desaparecido',
                                                        'Descarte' => 'Descarte',
                                                        'Estoque' => 'Estoque',
                                                        'Flagged' => 'Sinalizado',
                                                        'Instalado' => 'Instalado',
                                                        'Queimado' => 'Queimado',
                                                        'Verificar' => 'Verificar',
                                                        // Legacy translations for backward compatibility
                                                        'Active' => 'Ativo',
                                                        'Inactive' => 'Inativo',
                                                        'Maintenance' => 'Manutenção',
                                                        'Retired' => 'Aposentado'
                                                    ];
                                                    echo htmlspecialchars($statusTranslations[$status] ?? $status);
                                                    ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="inclusao" class="form-label">
                                            <i class="fas fa-calendar"></i> Data de Inclusão *
                                        </label>
                                        <input type="date" class="form-control" id="inclusao" name="inclusao" 
                                               value="<?= htmlspecialchars($item['inclusao']) ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Localização *
                                </label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= htmlspecialchars($item['location']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="obs" class="form-label">
                                    <i class="fas fa-sticky-note"></i> Observações
                                </label>
                                <textarea class="form-control" id="obs" name="obs" rows="3"><?= htmlspecialchars($item['obs']) ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
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
