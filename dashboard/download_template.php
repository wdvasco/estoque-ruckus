<?php
/**
 * Download Template CSV File
 * Generates and downloads a CSV template for inventory import
 */

require_once '../classes/User.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="template_estoque_ruckus.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create file pointer connected to PHP output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 (helps with Excel compatibility)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers
$headers = [
    'APMAC',
    'APName', 
    'Model',
    'Serial',
    'Status',
    'Location',
    'Inclusao',
    'Obs'
];

// Write headers
fputcsv($output, $headers);

// Sample data rows
$sampleData = [
    [
        '00:11:22:33:44:55',
        'AP-LOBBY-01',
        'R750',
        'RK7501234567',
        'Active',
        'Main Lobby - Floor 1',
        '2024-01-15',
        'Primary lobby access point'
    ],
    [
        '00:11:22:33:44:56',
        'AP-CONF-01',
        'R650',
        'RK6501234568',
        'Active',
        'Conference Room A - Floor 2',
        '2024-01-16',
        'Conference room coverage'
    ],
    [
        '00:11:22:33:44:57',
        'AP-OFFICE-01',
        'R550',
        'RK5501234569',
        'Maintenance',
        'Office Area - Floor 3',
        '2024-01-17',
        'Under maintenance - firmware update needed'
    ]
];

// Write sample data
foreach ($sampleData as $row) {
    fputcsv($output, $row);
}

// Close file pointer
fclose($output);
exit;
?>
