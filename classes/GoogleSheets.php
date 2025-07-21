<?php
/**
 * Google Sheets Integration Class - Public Sheet Version
 * Handles reading data from public Google Sheets
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/google_sheets.php';
require_once __DIR__ . '/Inventory.php';

class GoogleSheets {
    
    /**
     * Read data from Google Sheet
     * @return array Sheet data
     */
    public function readSheet() {
        try {
            return readPublicGoogleSheet();
        } catch (Exception $e) {
            throw new Exception('Erro ao ler planilha: ' . $e->getMessage());
        }
    }
    
    /**
     * Get sheet metadata
     * @return array Sheet information
     */
    public function getSheetInfo() {
        return getSheetInfo();
    }
    
    /**
     * Convert sheet data to inventory format
     * @param array $sheetData Raw sheet data
     * @return array Formatted inventory data
     */
    public function formatInventoryData($sheetData) {
        if (empty($sheetData) || count($sheetData) < 2) {
            return [];
        }
        
        $headers = array_map('trim', $sheetData[0]);
        $formattedData = [];
        
        // Process each row (skip header)
        for ($i = 1; $i < count($sheetData); $i++) {
            $row = $sheetData[$i];
            
            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Map columns based on your sheet structure
            $item = [
                'APMAC' => $row[0] ?? '',      // AP MAC
                'APName' => $row[1] ?? '',     // AP Name
                'Model' => $row[2] ?? '',      // Model
                'Serial' => $row[3] ?? '',     // Serial
                'Status' => $row[4] ?? '',     // Status
                'Location' => $row[5] ?? '',   // Location
                'Inclusao' => $row[6] ?? '',   // Registered On
                'Obs' => $row[7] ?? ''         // Observação
            ];
            
            $formattedData[] = $item;
        }
        
        return $formattedData;
    }
    
    /**
     * Import data from Google Sheet to database
     * @return array Import result
     */
    public function importFromSheet() {
        try {
            // Read data from sheet
            $sheetData = $this->readSheet();
            
            if (empty($sheetData)) {
                return [
                    'success' => false,
                    'message' => 'Planilha vazia ou sem dados'
                ];
            }
            
            // Format data
            $formattedData = $this->formatInventoryData($sheetData);
            
            if (empty($formattedData)) {
                return [
                    'success' => false,
                    'message' => 'Nenhum dado válido encontrado na planilha'
                ];
            }
            
            // Import to database using existing Inventory class
            $inventory = new Inventory();
            $result = $inventory->importFromArray($formattedData);
            
            // Add sheet information to result
            $sheetInfo = $this->getSheetInfo();
            $result['sheet_info'] = $sheetInfo;
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na importação: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get sheet preview (first 10 rows)
     * @return array Preview data
     */
    public function getSheetPreview() {
        try {
            $sheetData = $this->readSheet();
            
            if (empty($sheetData) || count($sheetData) < 2) {
                return [];
            }
            
            $headers = array_map('trim', $sheetData[0]);
            $preview = [];
            
            // Process up to 10 rows (skip header)
            for ($i = 1; $i < min(count($sheetData), 11); $i++) {
                $row = $sheetData[$i];
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $item = [];
                foreach ($headers as $index => $header) {
                    $item[$header] = $row[$index] ?? '';
                }
                
                $preview[] = $item;
            }
            
            return [
                'headers' => $headers,
                'data' => $preview,
                'total_rows' => count($sheetData) - 1
            ];
            
        } catch (Exception $e) {
            throw new Exception('Erro ao obter preview: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear cache
     * @return boolean Success status
     */
    public function clearCache() {
        if (file_exists(SHEET_CACHE_FILE)) {
            return unlink(SHEET_CACHE_FILE);
        }
        return true;
    }
}
?> 