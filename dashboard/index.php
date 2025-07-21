<?php
/**
 * Dashboard - Main inventory management page
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

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get inventory items
$result = $inventory->getAll($page, 5, $search);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Estoque Ruckus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }
        .nav-pills .nav-link {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-active { color: #28a745; }
        .status-inactive { color: #dc3545; }
        .status-maintenance { color: #ffc107; }
        .status-retired { color: #6c757d; }
        
        /* Improved Pagination Styles */
        .pagination {
            margin-top: 20px;
        }
        .pagination .page-link {
            color: #667eea;
            border: 1px solid #dee2e6;
            margin: 0 2px;
            border-radius: 5px;
        }
        .pagination .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #667eea;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        /* Action Buttons */
        .btn-action {
            margin: 1px;
            font-size: 0.8rem;
            padding: 4px 8px;
        }
        .btn-ficha {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }
        .btn-ficha:hover {
            background-color: #138496;
            border-color: #117a8b;
            color: white;
        }
        
        /* Responsive table */
        .table-responsive {
            overflow-x: auto;
        }
        @media (max-width: 768px) {
            .btn-action {
                display: block;
                width: 100%;
                margin: 2px 0;
            }
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
                            <a class="nav-link text-white active" href="index.php">
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
                        <a href="../index.php" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <h2 class="mb-0"><i class="fas fa-list"></i> Controle de Estoque</h2>
                    </div>
                    <a href="add_item.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Item
                    </a>
                </div>
                
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-<?= isset($_GET['type']) && $_GET['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                        <?= htmlspecialchars($_GET['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Buscar por MAC, Nome, Modelo, Serial ou Localização..." 
                                       value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Inventory Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if ($result['success'] && !empty($result['items'])): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>MAC Address</th>
                                            <th>Nome do AP</th>
                                            <th>Modelo</th>
                                            <th>Serial</th>
                                            <th>Status</th>
                                            <th>Localização</th>
                                            <th>Data Inclusão</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['items'] as $item): ?>
                                            <tr>
                                                <td><code><?= !empty($item['apmac']) ? htmlspecialchars($item['apmac']) : '<em class="text-muted">Vazio</em>' ?></code></td>
                                                <td><strong><?= !empty($item['apname']) ? htmlspecialchars($item['apname']) : '<em class="text-muted">Vazio</em>' ?></strong></td>
                                                <td><?= !empty($item['model']) ? htmlspecialchars($item['model']) : '<em class="text-muted">Vazio</em>' ?></td>
                                                <td><?= !empty($item['serial']) ? htmlspecialchars($item['serial']) : '<em class="text-muted">Vazio</em>' ?></td>
                                                <td>
                                                    <span class="status-<?= strtolower($item['status']) ?>">
                                                        <i class="fas fa-circle"></i> <?= htmlspecialchars($item['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= !empty($item['location']) ? htmlspecialchars($item['location']) : '<em class="text-muted">Vazio</em>' ?></td>
                                                <td><?= !empty($item['inclusao']) && $item['inclusao'] !== '0000-00-00' ? date('d/m/Y', strtotime($item['inclusao'])) : '<em class="text-muted">Vazio</em>' ?></td>
                                                <td>
                                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                                        <a href="view_item.php?id=<?= $item['id'] ?>" class="btn btn-ficha btn-action" title="Ver Ficha">
                                                            <i class="fas fa-id-card"></i> Ficha
                                                        </a>
                                                        <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-action" title="Editar">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <a href="delete_item.php?id=<?= $item['id'] ?>" 
                                                           class="btn btn-danger btn-action"
                                                           onclick="return confirm('Tem certeza que deseja excluir este item?')"
                                                           title="Excluir">
                                                            <i class="fas fa-trash"></i> Excluir
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Improved Pagination -->
                            <?php if ($result['pagination']['total_pages'] > 1): ?>
                                <nav aria-label="Navegação de páginas">
                                    <ul class="pagination justify-content-center flex-wrap">
                                        <!-- First Page -->
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" title="Primeira página">
                                                    <i class="fas fa-angle-double-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Previous Page -->
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" title="Página anterior">
                                                    <i class="fas fa-angle-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Page Numbers -->
                                        <?php
                                        $startPage = max(1, $page - 2);
                                        $endPage = min($result['pagination']['total_pages'], $page + 2);
                                        
                                        // Show first page if not in range
                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">1</a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif;
                                        endif;
                                        
                                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor;
                                        
                                        // Show last page if not in range
                                        if ($endPage < $result['pagination']['total_pages']): ?>
                                            <?php if ($endPage < $result['pagination']['total_pages'] - 1): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $result['pagination']['total_pages'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                                    <?= $result['pagination']['total_pages'] ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Next Page -->
                                        <?php if ($page < $result['pagination']['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" title="Próxima página">
                                                    <i class="fas fa-angle-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Last Page -->
                                        <?php if ($page < $result['pagination']['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $result['pagination']['total_pages'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" title="Última página">
                                                    <i class="fas fa-angle-double-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                    
                                    <!-- Pagination Info -->
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            Página <?= $page ?> de <?= $result['pagination']['total_pages'] ?> 
                                            (<?= $result['pagination']['total_items'] ?> itens no total)
                                        </small>
                                    </div>
                                </nav>
                            <?php endif; ?>
                            
                        <?php elseif ($result['success'] && empty($result['items'])): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Nenhum item encontrado</h5>
                                <p class="text-muted">
                                    <?= !empty($search) ? 'Nenhum item corresponde à sua busca.' : 'Comece adicionando itens ao estoque.' ?>
                                </p>
                                <a href="add_item.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Adicionar Primeiro Item
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Erro ao carregar itens: <?= htmlspecialchars($result['message']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
