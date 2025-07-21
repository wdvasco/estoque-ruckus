<?php
/**
 * Google Sheets Import Process Handler - Public Sheet Version
 */

require_once '../classes/User.php';
require_once '../classes/GoogleSheets.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: import_google_sheets.php');
    exit;
}

try {
    // Clear cache to get fresh data
    $googleSheets = new GoogleSheets();
    $googleSheets->clearCache();
    
    // Import data from Google Sheets
    $result = $googleSheets->importFromSheet();
    
    if ($result['success']) {
        $message = $result['message'];
        
        // Add sheet information
        if (isset($result['sheet_info'])) {
            $message .= ' | Planilha: ' . $result['sheet_info']['data_rows'] . ' linhas processadas';
            $message .= ' | Atualizado: ' . $result['sheet_info']['last_updated'];
        }
        
        // Add statistics
        if (isset($result['stats'])) {
            $message .= ' | Importados: ' . $result['stats']['imported'];
            $message .= ' | Ignorados: ' . $result['stats']['skipped'];
            
            if (!empty($result['stats']['unique_models'])) {
                $message .= ' | Modelos: ' . implode(', ', array_slice($result['stats']['unique_models'], 0, 5));
                if (count($result['stats']['unique_models']) > 5) {
                    $message .= ' (+' . (count($result['stats']['unique_models']) - 5) . ' mais)';
                }
            }
        }
        
        header('Location: index.php?message=' . urlencode($message) . '&type=success');
    } else {
        header('Location: import_google_sheets.php?message=' . urlencode($result['message']) . '&type=error');
    }
    
} catch (Exception $e) {
    header('Location: import_google_sheets.php?message=' . urlencode('Erro na importação: ' . $e->getMessage()) . '&type=error');
}
exit;
?> 