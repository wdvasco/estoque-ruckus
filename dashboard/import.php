<?php
/**
 * Import Excel/CSV Page
 */

require_once '../classes/User.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

$currentUser = $user->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Excel - Sistema de Estoque Ruckus</title>
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
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .upload-area.dragover {
            border-color: #667eea;
            background-color: #f0f4ff;
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
                            <a class="nav-link text-white active" href="import.php">
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
                    <h2><i class="fas fa-upload"></i> Importar Planilha</h2>
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
                
                <!-- Upload Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-excel"></i> Upload de Arquivo</h5>
                    </div>
                    <div class="card-body">
                        <form action="import_process.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Arraste e solte seu arquivo aqui</h5>
                                <p class="text-muted">ou clique para selecionar</p>
                                <input type="file" class="form-control" id="file" name="file" 
                                       accept=".xlsx,.xls,.csv" required style="display: none;">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('file').click()">
                                    <i class="fas fa-folder-open"></i> Selecionar Arquivo
                                </button>
                            </div>
                            
                            <div class="mt-3" id="fileInfo" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-file"></i> <span id="fileName"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="clearFile()">
                                        <i class="fas fa-times"></i> Remover
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="uploadBtn" disabled>
                                    <i class="fas fa-upload"></i> Importar Planilha
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Instruções de Importação</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-file-excel"></i> Formatos Aceitos</h6>
                                <ul>
                                    <li>Excel (.xlsx, .xls)</li>
                                    <li>CSV (.csv)</li>
                                </ul>
                                
                                <h6 class="mt-3"><i class="fas fa-columns"></i> Colunas Obrigatórias</h6>
                                <ul>
                                    <li><strong>APMAC</strong> - MAC Address do AP</li>
                                    <li><strong>APName</strong> - Nome do Access Point</li>
                                    <li><strong>Model</strong> - Modelo do equipamento</li>
                                    <li><strong>Serial</strong> - Número serial</li>
                                    <li><strong>Status</strong> - Status operacional</li>
                                    <li><strong>Location</strong> - Localização física</li>
                                    <li><strong>Inclusao</strong> - Data de inclusão</li>
                                    <li><strong>Obs</strong> - Observações (opcional)</li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h6><i class="fas fa-download"></i> Modelo de Planilha</h6>
                                <p>Baixe o modelo de planilha para garantir a formatação correta:</p>
                                <a href="download_template.php" class="btn btn-success">
                                    <i class="fas fa-download"></i> Baixar Modelo Excel
                                </a>
                                
                                <h6 class="mt-3"><i class="fas fa-exclamation-triangle"></i> Observações</h6>
                                <ul class="text-muted">
                                    <li>A primeira linha deve conter os cabeçalhos</li>
                                    <li>MAC Address deve estar no formato XX:XX:XX:XX:XX:XX</li>
                                    <li>Itens com MAC ou Serial duplicados serão ignorados</li>
                                    <li>Status aceitos: Active, Inactive, Maintenance, Retired</li>
                                    <li>Data deve estar no formato YYYY-MM-DD</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('file');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const uploadBtn = document.getElementById('uploadBtn');
        
        // Handle drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFileInfo(files[0]);
            }
        });
        
        // Handle file selection
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showFileInfo(e.target.files[0]);
            }
        });
        
        function showFileInfo(file) {
            fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            fileInfo.style.display = 'block';
            uploadBtn.disabled = false;
        }
        
        function clearFile() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            uploadBtn.disabled = true;
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
</body>
</html>
