<?php
/**
 * Google Sheets Configuration - Public Sheet Version
 * Ruckus Access Points Inventory System
 */

// Your public Google Sheet ID
define('GOOGLE_SHEET_ID', '12yHevEiJJTfT_PPn8zcyW8yQjzlY0L_4fcx2JOguAxw');

// Sheet range to read (adjust based on your data)
define('GOOGLE_SHEET_RANGE', 'A:H');

// Cache settings
define('SHEET_CACHE_DURATION', 300); // 5 minutes cache
define('SHEET_CACHE_FILE', __DIR__ . '/sheet_cache.json');

/**
 * Read data from public Google Sheet using working method
 * @return array Sheet data
 */
function readPublicGoogleSheet() {
    // Check cache first
    if (file_exists(SHEET_CACHE_FILE)) {
        $cache = json_decode(file_get_contents(SHEET_CACHE_FILE), true);
        if ($cache['timestamp'] > (time() - SHEET_CACHE_DURATION)) {
            return $cache['data'];
        }
    }
    
    // Use the working method (without gid=0 parameter)
    $url = "https://docs.google.com/spreadsheets/d/" . GOOGLE_SHEET_ID . "/export?format=csv";
    
    // Set up context for HTTP request
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: text/csv,text/plain,*/*',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                'Cache-Control: no-cache'
            ],
            'timeout' => 30
        ]
    ]);
    
    // Read CSV data
    $csvData = file_get_contents($url, false, $context);
    if ($csvData === false || empty($csvData)) {
        throw new Exception('Erro ao acessar a planilha Google Sheets. URL: ' . $url);
    }
    
    // Parse CSV data
    $lines = explode("\n", $csvData);
    $data = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            // Use str_getcsv to properly handle CSV parsing
            $row = str_getcsv($line);
            if (!empty($row)) {
                $data[] = $row;
            }
        }
    }
    
    // Validate that we have at least header and some data
    if (count($data) < 2) {
        throw new Exception('Planilha vazia ou sem dados válidos. Linhas encontradas: ' . count($data));
    }
    
    // Cache the data
    $cache = [
        'timestamp' => time(),
        'data' => $data
    ];
    file_put_contents(SHEET_CACHE_FILE, json_encode($cache));
    
    return $data;
}

/**
 * Test Google Sheets connection
 * @return boolean True if connection successful
 */
function testGoogleSheetsConnection() {
    try {
        $data = readPublicGoogleSheet();
        return !empty($data) && count($data) > 1; // At least header + 1 data row
    } catch (Exception $e) {
        error_log('Google Sheets connection test failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get sheet metadata
 * @return array Sheet information
 */
function getSheetInfo() {
    try {
        $data = readPublicGoogleSheet();
        return [
            'total_rows' => count($data),
            'data_rows' => count($data) - 1, // Exclude header
            'headers' => $data[0] ?? [],
            'last_updated' => file_exists(SHEET_CACHE_FILE) ? 
                date('d/m/Y H:i:s', json_decode(file_get_contents(SHEET_CACHE_FILE), true)['timestamp']) : 
                'Agora'
        ];
    } catch (Exception $e) {
        error_log('Failed to get sheet info: ' . $e->getMessage());
        return null;
    }
}

/**
 * Manual test function for debugging
 */
function debugGoogleSheetsAccess() {
    echo "<h3>🔍 Debug: Testando Acesso à Planilha</h3>";
    
    $methods = [
        "Método Funcionando (CSV Export)" => "https://docs.google.com/spreadsheets/d/" . GOOGLE_SHEET_ID . "/export?format=csv",
        "Método Alternativo (CSV Export com gid)" => "https://docs.google.com/spreadsheets/d/" . GOOGLE_SHEET_ID . "/export?format=csv&gid=0",
        "Método Público (se configurado)" => "https://docs.google.com/spreadsheets/d/" . GOOGLE_SHEET_ID . "/pub?output=csv"
    ];
    
    foreach ($methods as $name => $url) {
        echo "<h4>$name</h4>";
        echo "<p><strong>URL:</strong> <a href='$url' target='_blank'>$url</a></p>";
        
        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept: text/csv,text/plain,*/*'
                    ],
                    'timeout' => 10
                ]
            ]);
            
            $response = file_get_contents($url, false, $context);
            
            if ($response !== false) {
                echo "<p style='color: green;'>✅ Sucesso! Tamanho: " . strlen($response) . " bytes</p>";
                echo "<p><strong>Primeiras 200 caracteres:</strong></p>";
                echo "<pre>" . htmlspecialchars(substr($response, 0, 200)) . "...</pre>";
            } else {
                echo "<p style='color: red;'>❌ Falha na requisição</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        echo "<hr>";
    }
}
?> 