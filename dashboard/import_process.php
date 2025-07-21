<?php
/**
 * Import Process Handler
 * Processes Excel/CSV file upload and imports data
 */

require_once '../classes/User.php';
require_once '../classes/Inventory.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: import.php');
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: import.php?message=' . urlencode('Erro no upload do arquivo') . '&type=error');
    exit;
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Validate file extension
if (!in_array($fileExtension, ['xlsx', 'xls', 'csv'])) {
    header('Location: import.php?message=' . urlencode('Formato de arquivo não suportado. Use Excel (.xlsx, .xls) ou CSV (.csv)') . '&type=error');
    exit;
}

try {
    $data = [];
    
    if ($fileExtension === 'csv') {
        // Process CSV file (using semicolon delimiter as per the provided file)
        if (($handle = fopen($fileTmpName, 'r')) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ';');
            
            // Map headers to expected format
            $headerMap = [];
            foreach ($headers as $index => $header) {
                $headerMap[$index] = trim($header);
            }
            
            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
                $rowData = [];
                foreach ($row as $index => $value) {
                    $headerName = $headerMap[$index] ?? $index;
                    $rowData[$headerName] = trim($value);
                }
                $data[] = $rowData;
            }
            fclose($handle);
        }
    } else {
        // Process Excel file using SimpleXLSX (lightweight Excel reader)
        // For this implementation, we'll create a simple Excel parser
        // In production, you might want to use PhpSpreadsheet library
        
        // For now, let's show an error message suggesting CSV format
        header('Location: import.php?message=' . urlencode('Para esta versão, use arquivos CSV. Converta seu Excel para CSV e tente novamente.') . '&type=error');
        exit;
    }
    
    if (empty($data)) {
        header('Location: import.php?message=' . urlencode('Arquivo vazio ou sem dados válidos') . '&type=error');
        exit;
    }
    
    // Import data using Inventory class
    $inventory = new Inventory();
    $result = $inventory->importFromArray($data);
    
    if ($result['success']) {
        $message = $result['message'];
        
        // Add information about unique values found
        if (!empty($result['stats']['unique_models'])) {
            $message .= ' | Modelos encontrados: ' . implode(', ', $result['stats']['unique_models']);
        }
        if (!empty($result['stats']['unique_statuses'])) {
            $message .= ' | Status encontrados: ' . implode(', ', $result['stats']['unique_statuses']);
        }
        
        if (!empty($result['stats']['errors'])) {
            $message .= ' Erros: ' . implode('; ', array_slice($result['stats']['errors'], 0, 3));
            if (count($result['stats']['errors']) > 3) {
                $message .= ' (e mais ' . (count($result['stats']['errors']) - 3) . ' erros)';
            }
        }
        header('Location: index.php?message=' . urlencode($message) . '&type=success');
    } else {
        header('Location: import.php?message=' . urlencode($result['message']) . '&type=error');
    }
    
} catch (Exception $e) {
    header('Location: import.php?message=' . urlencode('Erro ao processar arquivo: ' . $e->getMessage()) . '&type=error');
}
exit;
?>
