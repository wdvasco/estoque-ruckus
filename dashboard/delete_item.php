<?php
/**
 * Delete Item Handler
 * Processes inventory item deletion
 */

require_once '../classes/User.php';
require_once '../classes/Inventory.php';

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../auth/login.php?message=' . urlencode('Acesso negado. Faça login primeiro.'));
    exit;
}

// Get item ID
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($itemId <= 0) {
    header('Location: index.php?message=' . urlencode('ID do item inválido') . '&type=error');
    exit;
}

// Try to delete the item
$inventory = new Inventory();
$result = $inventory->delete($itemId);

if ($result['success']) {
    // Item deleted successfully
    header("Location: index.php?message=" . urlencode($result['message']) . "&type=success");
} else {
    // Item deletion failed
    header("Location: index.php?message=" . urlencode($result['message']) . "&type=error");
}
exit;
?>
