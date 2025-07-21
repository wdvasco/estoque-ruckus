<?php
/**
 * User Registration Handler
 * Processes user registration form submission
 */

require_once '../classes/User.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate input
$errors = [];

if (empty($name)) {
    $errors[] = 'Nome é obrigatório';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail válido é obrigatório';
}

if (empty($password) || strlen($password) < 6) {
    $errors[] = 'Senha deve ter pelo menos 6 caracteres';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Senhas não coincidem';
}

// If there are validation errors, redirect back with error message
if (!empty($errors)) {
    $message = implode(', ', $errors);
    header("Location: ../index.php?message=" . urlencode($message) . "&type=error");
    exit;
}

// Try to register user
$user = new User();
$result = $user->register($name, $email, $password);

if ($result['success']) {
    // Registration successful, redirect to login page
    header("Location: login.php?message=" . urlencode($result['message']) . "&type=success");
} else {
    // Registration failed, redirect back with error message
    header("Location: ../index.php?message=" . urlencode($result['message']) . "&type=error");
}
exit;
?>
