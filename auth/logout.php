<?php
/**
 * Logout Handler
 * Destroys user session and redirects to login page
 */

require_once '../classes/User.php';

$user = new User();
$user->logout();

header("Location: login.php?message=" . urlencode('Logout realizado com sucesso') . "&type=success");
exit;
?>
