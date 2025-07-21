<?php
/**
 * Google Sheets Import Page - Public Sheet Version
 */

require_once '../classes/User.php';
require_once '../classes/GoogleSheets.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

$currentUser = $user->getCurrentUser();
$error = null;
$preview = null;
$sheetInfo = null;

// Try to get sheet preview
try {
    $googleSheets = new GoogleSheets();
    $preview = $googleSheets->getSheetPreview();
    $sheetInfo = $googleSheets->getSheetInfo();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Google Sheets - Sistema de Estoque Ruckus</title>
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
        .sheet-preview {
            max-height: 400px;
            overflow-y: auto;
        }
        .status-connected { color: #28a745; }
        .status-error { color: #dc3545; }
        .sheet-link {
            color: #007bff;
            text-decoration: none;
        }
        .sheet-link:hover {
            text-decoration: underline;
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
                                <i class="fas fa-upload"></i> Importar CSV
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="import_google_sheets.php">
                                <i class="fab fa-google"></i> Google Sheets
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
                    <h2><i class="fab fa-google"></i> Importar Google Sheets</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Erro de Conexão</h5>
                        <p><?= htmlspecialchars($error) ?></p>
                        <hr>
                        <p><strong>Planilha:</strong> 
                            <a href="https://docs.google.com/spreadsheets/d/12yHevEiJJTfT_PPn8zcyW8yQjzlY0L_4fcx2JOguAxw/edit?usp=sharing" 
                               target="_blank" class="sheet-link">
                                Gestão de APs
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Connection Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-link"></i> Status da Conexão</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><i class="fas fa-check-circle status-connected"></i> 
                                       <strong>Conectado ao Google Sheets</strong></p>
                                    <p><strong>Planilha:</strong> 
                                        <a href="https://docs.google.com/spreadsheets/d/12yHevEiJJTfT_PPn8zcyW8yQjzlY0L_4fcx2JOguAxw/edit?usp=sharing" 
                                           target="_blank" class="sheet-link">
                                            Gestão de APs
                                        </a>
                                    </p>
                                    <p><strong>Total de linhas:</strong> <?= $sheetInfo['data_rows'] ?? 0 ?></p>
                                    <p><strong>Última atualização:</strong> <?= $sheetInfo['last_updated'] ?? 'Agora' ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Colunas detectadas:</strong></p>
                                    <ul>
                                        <?php foreach ($sheetInfo['headers'] ?? [] as $header): ?>
                                            <li><?= htmlspecialchars($header) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-eye"></i> Preview dos Dados (Primeiras 10 linhas)</h5>
                            <button onclick="refreshPreview()" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-sync-alt"></i> Atualizar
                            </button>
                        </div>
                        <div class="card-body sheet-preview">
                            <?php if (!empty($preview['data'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <?php foreach ($preview['headers'] as $header): ?>
                                                    <th><?= htmlspecialchars($header) ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($preview['data'] as $row): ?>
                                                <tr>
                                                    <?php foreach ($preview['headers'] as $header): ?>
                                                        <td><?= htmlspecialchars($row[$header] ?? '') ?></td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Nenhum dado encontrado na planilha.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Import Button -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-download"></i> Importar Dados</h5>
                        </div>
                        <div class="card-body">
                            <p>Clique no botão abaixo para importar todos os dados da planilha para o sistema.</p>
                            <p class="text-muted">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    Os dados são atualizados automaticamente a cada 5 minutos.
                                </small>
                            </p>
                            <form action="import_google_sheets_process.php" method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja importar os dados? Esta ação pode demorar alguns minutos.')">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-download"></i> Importar do Google Sheets
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshPreview() {
            location.reload();
        }
    </script>
</body>
</html> 