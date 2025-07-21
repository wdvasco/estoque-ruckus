<?php
/**
 * Login Process Handler
 * Processes user login form submission
 */

require_once '../classes/User.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    header("Location: login.php?message=" . urlencode('E-mail e senha são obrigatórios') . "&type=error");
    exit;
}

// Try to authenticate user
$user = new User();
$result = $user->login($email, $password);

if ($result['success']) {
    // Login successful, redirect to dashboard
    header("Location: ../dashboard/index.php");
} else {
    // Login failed, redirect back with error message
    header("Location: login.php?message=" . urlencode($result['message']) . "&type=error");
}
exit;
?>
