<?php
/**
 * Edit Item Process Handler
 * Processes inventory item edit form submission
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
    header('Location: index.php');
    exit;
}

// Get item ID
$itemId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($itemId <= 0) {
    header('Location: index.php?message=' . urlencode('ID do item inválido') . '&type=error');
    exit;
}

// Get form data
$data = [
    'apmac' => strtoupper(trim($_POST['apmac'] ?? '')),
    'apname' => trim($_POST['apname'] ?? ''),
    'model' => trim($_POST['model'] ?? ''),
    'serial' => trim($_POST['serial'] ?? ''),
    'status' => trim($_POST['status'] ?? 'Active'),
    'location' => trim($_POST['location'] ?? ''),
    'inclusao' => trim($_POST['inclusao'] ?? date('Y-m-d')),
    'obs' => trim($_POST['obs'] ?? '')
];

// Validate required fields
$errors = [];

if (empty($data['apmac']) || !preg_match('/^[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}$/', $data['apmac'])) {
    $errors[] = 'MAC Address é obrigatório e deve estar no formato XX:XX:XX:XX:XX:XX';
}

if (empty($data['apname'])) {
    $errors[] = 'Nome do AP é obrigatório';
}

if (empty($data['model'])) {
    $errors[] = 'Modelo é obrigatório';
}

if (empty($data['serial'])) {
    $errors[] = 'Número Serial é obrigatório';
}

if (empty($data['location'])) {
    $errors[] = 'Localização é obrigatória';
}

if (!empty($data['inclusao']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['inclusao'])) {
    $errors[] = 'Data de inclusão deve estar no formato YYYY-MM-DD';
}

// If there are validation errors, redirect back with error message
if (!empty($errors)) {
    $message = implode(', ', $errors);
    header("Location: edit_item.php?id=$itemId&message=" . urlencode($message) . "&type=error");
    exit;
}

// Try to update the item
$inventory = new Inventory();
$result = $inventory->update($itemId, $data);

if ($result['success']) {
    // Item updated successfully
    header("Location: index.php?message=" . urlencode($result['message']) . "&type=success");
} else {
    // Item update failed
    header("Location: edit_item.php?id=$itemId&message=" . urlencode($result['message']) . "&type=error");
}
exit;
?>
