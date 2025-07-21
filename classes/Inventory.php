<?php
/**
 * Inventory Class
 * Handles Access Points inventory management (CRUD operations)
 */

// Database connection will be handled by the calling script

class Inventory {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getConnection();
    }
    
    /**
     * Format MAC Address to XXXXXXXXXXXX format
     * @param string $mac MAC address in any format
     * @return string Formatted MAC address
     */
    private function formatMacAddress($mac) {
        if (empty($mac)) {
            return '';
        }
        
        // Remove MAC- prefix if present
        $mac = preg_replace('/^MAC-?/i', '', $mac);
        
        // Remove all non-alphanumeric characters
        $cleanMac = preg_replace('/[^0-9A-Fa-f]/', '', $mac);
        
        // Ensure it's 12 characters long
        if (strlen($cleanMac) === 12) {
            return strtoupper($cleanMac);
        }
        
        // If not 12 characters, return original for manual review
        return $mac;
    }
    
    /**
     * Format date preserving original format from Google Sheets
     * @param string $date Date in various formats
     * @return string Original date format or empty string
     */
    private function formatDate($date) {
        if (empty($date)) {
            return null; // Return NULL instead of current date
        }
        
        // Clean the date string
        $date = trim($date);
        
        // If it's already in a valid format, return as is
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            // Convert DD/MM/YYYY to YYYY-MM-DD for MySQL
            $parts = explode('/', $date);
            if (count($parts) === 3) {
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        }
        
        // Try to parse various date formats
        $formats = [
            'Y-m-d',     // 2024-01-15
            'd/m/Y',     // 15/01/2024
            'd-m-Y',     // 15-01-2024
            'm/d/Y',     // 01/15/2024
            'Y/m/d',     // 2024/01/15
            'd.m.Y',     // 15.01.2024
        ];
        
        foreach ($formats as $format) {
            $dateObj = DateTime::createFromFormat($format, $date);
            if ($dateObj !== false) {
                return $dateObj->format('Y-m-d'); // Convert to MySQL format
            }
        }
        
        // If no format matches, try strtotime as fallback
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        // If all fails, return NULL
        return null;
    }
    
    /**
     * Create new inventory item - FLEXIBLE VERSION for import
     * Handles duplicates more gracefully and allows blank fields
     * @param array $data Item data
     * @return array Result with success status and message
     */
    public function createFlexible($data) {
        try {
            // For flexible import, we're more lenient with duplicates
            // Only check for exact duplicates of non-empty values
            $checkConditions = [];
            $checkParams = [];
            
            // Only check MAC if it's not empty
            if (!empty($data['apmac'])) {
                $checkConditions[] = "apmac = ?";
                $checkParams[] = $data['apmac'];
            }
            
            // Only check Serial if it's not empty
            if (!empty($data['serial'])) {
                $checkConditions[] = "serial = ?";
                $checkParams[] = $data['serial'];
            }
            
            // Only check for duplicates if we have values to check
            if (!empty($checkConditions)) {
                $stmt = $this->pdo->prepare("SELECT id FROM estoque WHERE " . implode(' OR ', $checkConditions));
                $stmt->execute($checkParams);
                
                if ($stmt->rowCount() > 0) {
                    // For import, we'll skip duplicates instead of failing
                    return ['success' => false, 'message' => 'Item já existe (ignorado)'];
                }
            }
            
            $stmt = $this->pdo->prepare("
                INSERT INTO estoque (apmac, apname, model, serial, status, location, inclusao, obs) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['apmac'] ?: null,
                $data['apname'] ?: null,
                $data['model'] ?: null,
                $data['serial'] ?: null,
                $data['status'] ?: 'Instalado',
                $data['location'] ?: null,
                $data['inclusao'] ?: null,
                $data['obs'] ?: null
            ]);
            
            return ['success' => true, 'message' => 'Item importado com sucesso', 'id' => $this->pdo->lastInsertId()];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao importar item: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create new inventory item
     * @param array $data Item data
     * @return array Result with success status and message
     */
    public function create($data) {
        try {
            // Format MAC address before checking for duplicates
            $formattedMac = $this->formatMacAddress($data['apmac']);
            
            // Check if MAC address or Serial already exists
            $stmt = $this->pdo->prepare("SELECT id FROM estoque WHERE apmac = ? OR serial = ?");
            $stmt->execute([$formattedMac, $data['serial']]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'MAC Address ou Serial já cadastrado'];
            }
            
            $stmt = $this->pdo->prepare("
                INSERT INTO estoque (apmac, apname, model, serial, status, location, inclusao, obs) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            
            ");
            
            $stmt->execute([
                $formattedMac,
                $data['apname'],
                $data['model'],
                $data['serial'],
                $data['status'],
                $data['location'],
                $data['inclusao'],
                $data['obs']
            ]);
            
            return ['success' => true, 'message' => 'Item cadastrado com sucesso', 'id' => $this->pdo->lastInsertId()];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar item: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all inventory items with pagination
     * @param int $page Current page number
     * @param int $limit Items per page
     * @param string $search Optional search term
     * @return array Items and pagination info
     */
    public function getAll($page = 1, $limit = 10, $search = '') {
        try {
            $offset = ($page - 1) * $limit;
            
            // Build search condition
            $searchCondition = '';
            $params = [];
            if (!empty($search)) {
                $searchCondition = "WHERE apmac LIKE ? OR apname LIKE ? OR model LIKE ? OR serial LIKE ? OR location LIKE ?";
                $searchTerm = "%$search%";
                $params = array_fill(0, 5, $searchTerm);
            }
            
            // Get total count
            $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM estoque $searchCondition");
            $countStmt->execute($params);
            $totalItems = $countStmt->fetchColumn();
            
            // Get items
            $stmt = $this->pdo->prepare("
                SELECT * FROM estoque 
                $searchCondition 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            $items = $stmt->fetchAll();
            
            return [
                'success' => true,
                'items' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'total_items' => $totalItems,
                    'items_per_page' => $limit,
                    'total_pages' => ceil($totalItems / $limit)
                ]
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao buscar itens: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get single inventory item by ID
     * @param int $id Item ID
     * @return array Item data or error
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM estoque WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            
            if ($item) {
                return ['success' => true, 'item' => $item];
            } else {
                return ['success' => false, 'message' => 'Item não encontrado'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao buscar item: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update inventory item
     * @param int $id Item ID
     * @param array $data Updated data
     * @return array Result with success status and message
     */
    public function update($id, $data) {
        try {
            // Format MAC address before checking for duplicates
            $formattedMac = $this->formatMacAddress($data['apmac']);
            
            // Check if MAC address or Serial already exists for other items
            $stmt = $this->pdo->prepare("SELECT id FROM estoque WHERE (apmac = ? OR serial = ?) AND id != ?");
            $stmt->execute([$formattedMac, $data['serial'], $id]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'MAC Address ou Serial já cadastrado em outro item'];
            }
            
            $stmt = $this->pdo->prepare("
                UPDATE estoque 
                SET apmac = ?, apname = ?, model = ?, serial = ?, status = ?, location = ?, inclusao = ?, obs = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $formattedMac,
                $data['apname'],
                $data['model'],
                $data['serial'],
                $data['status'],
                $data['location'],
                $data['inclusao'],
                $data['obs'],
                $id
            ]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Item atualizado com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Nenhuma alteração foi feita ou item não encontrado'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar item: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete inventory item
     * @param int $id Item ID
     * @return array Result with success status and message
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM estoque WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Item excluído com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Item não encontrado'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao excluir item: ' . $e->getMessage()];
        }
    }
    
    /**
     * Import data from array - UPDATED VERSION
     * Allows empty fields and preserves original data
     * @param array $data Array of data to import
     * @return array Result with success status and message
     */
    public function importFromArray($data) {
        try {
            $imported = 0;
            $skipped = 0;
            $errors = [];
            $uniqueModels = [];
            $uniqueStatuses = [];
            $totalRows = 0;
            
            foreach ($data as $index => $row) {
                $totalRows++;
                
                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    $skipped++;
                    continue;
                }
                
                // Map CSV columns to database fields - preserve original values
                $item = [
                    'apmac' => trim($row['APMAC'] ?? $row[0] ?? ''),
                    'apname' => trim($row['APName'] ?? $row[1] ?? ''),
                    'model' => trim($row['Model'] ?? $row[2] ?? ''),
                    'serial' => trim($row['Serial'] ?? $row[3] ?? ''),
                    'status' => trim($row['Status'] ?? $row[4] ?? '') ?: 'Instalado',
                    'location' => trim($row['Location'] ?? $row[5] ?? ''),
                    'inclusao' => $this->formatDate(trim($row['Inclusao'] ?? $row[6] ?? '')),
                    'obs' => trim($row['Obs'] ?? $row[7] ?? '')
                ];
                
                // Collect unique models and statuses for options
                if (!empty($item['model']) && !in_array($item['model'], $uniqueModels)) {
                    $uniqueModels[] = $item['model'];
                }
                
                if (!empty($item['status']) && !in_array($item['status'], $uniqueStatuses)) {
                    $uniqueStatuses[] = $item['status'];
                }
                
                // IMPORTANT: Don't generate automatic values for empty fields
                // Let them be NULL in the database
                
                // Try to import with flexible create method
                $result = $this->createFlexible($item);
                if ($result['success']) {
                    $imported++;
                } else {
                    $errors[] = "Linha " . ($index + 1) . ": " . $result['message'];
                    $skipped++;
                }
            }
            
            // Save unique models and statuses to database for future use
            $this->saveUniqueOptions($uniqueModels, $uniqueStatuses);
            
            return [
                'success' => true,
                'message' => "Importação concluída: $imported importados, $skipped ignorados",
                'stats' => [
                    'imported' => $imported,
                    'skipped' => $skipped,
                    'errors' => $errors,
                    'unique_models' => $uniqueModels,
                    'unique_statuses' => $uniqueStatuses
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro na importação: ' . $e->getMessage()];
        }
    }
    
    /**
     * Save unique models and statuses as options for future use
     * @param array $models Unique models found during import
     * @param array $statuses Unique statuses found during import
     */
    private function saveUniqueOptions($models, $statuses) {
        try {
            // Limpar todos os modelos antigos antes de inserir os novos
            $stmt = $this->pdo->prepare("DELETE FROM inventory_options WHERE option_type = 'model'");
            $stmt->execute();
            // Salvar modelos únicos
            foreach ($models as $model) {
                if (!empty($model)) {
                    $stmt = $this->pdo->prepare("
                        INSERT IGNORE INTO inventory_options (option_type, option_value) 
                        VALUES ('model', ?)
                    ");
                    $stmt->execute([$model]);
                }
            }
            // Salvar status únicos (mantém status antigos para não perder opções personalizadas)
            foreach ($statuses as $status) {
                if (!empty($status)) {
                    $stmt = $this->pdo->prepare("
                        INSERT IGNORE INTO inventory_options (option_type, option_value) 
                        VALUES ('status', ?)
                    ");
                    $stmt->execute([$status]);
                }
            }
        } catch (PDOException $e) {
            // Log error but don't fail the import
            error_log('Error saving unique options: ' . $e->getMessage());
        }
    }
    
    /**
     * Get available options for models
     * @return array List of available models
     */
    public function getModelOptions() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT option_value 
                FROM inventory_options 
                WHERE option_type = 'model' AND is_active = 1 
                ORDER BY option_value
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return ['R750', 'R650', 'R550', 'R350', 'R320']; // Fallback defaults
        }
    }
    
    /**
     * Get available options for statuses
     * @return array List of available statuses
     */
    public function getStatusOptions() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT option_value 
                FROM inventory_options 
                WHERE option_type = 'status' AND is_active = 1 
                ORDER BY option_value
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return ['Instalado', 'Estoque', 'Desaparecido', 'Queimado', 'Descarte', 'Verificar', 'Flagged']; // Fallback defaults
        }
    }

    /**
     * Retorna a distribuição de APs por status
     * @return array
     */
    public function getStatusDistribution() {
        $result = [];
        try {
            $stmt = $this->pdo->query("SELECT status, COUNT(*) as total FROM estoque GROUP BY status ORDER BY total DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['status']] = (int)$row['total'];
            }
        } catch (Exception $e) {}
        return $result;
    }

    /**
     * Retorna os top modelos de APs (limitado, padrão 10)
     * @param int $limit
     * @return array
     */
    public function getTopModels($limit = 10) {
        $result = [];
        try {
            $stmt = $this->pdo->prepare("SELECT model, COUNT(*) as total FROM estoque GROUP BY model ORDER BY total DESC LIMIT ?");
            $stmt->bindValue(1, (int)$limit, \PDO::PARAM_INT);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['model']] = (int)$row['total'];
            }
        } catch (Exception $e) {}
        return $result;
    }

    /**
     * Retorna a evolução de inclusões por ano
     * @return array
     */
    public function getYearlyEvolution() {
        $result = [];
        try {
            $stmt = $this->pdo->query("SELECT YEAR(inclusao) as ano, COUNT(*) as total FROM estoque WHERE inclusao IS NOT NULL AND inclusao != '0000-00-00' GROUP BY ano ORDER BY ano");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['ano']] = (int)$row['total'];
            }
        } catch (Exception $e) {}
        return $result;
    }

    /**
     * Retorna a quantidade de APs em estoque (status = 'Estoque') por modelo
     * @return array
     */
    public function getStockByModel() {
        $result = [];
        try {
            $stmt = $this->pdo->query("SELECT model, COUNT(*) as total FROM estoque WHERE status = 'Estoque' GROUP BY model ORDER BY total DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['model']] = (int)$row['total'];
            }
        } catch (Exception $e) {}
        return $result;
    }
}
?>
