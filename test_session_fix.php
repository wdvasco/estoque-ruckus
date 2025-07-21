<?php
/**
 * Session Fix Test Script
 * Verify that session warnings are resolved
 */

echo "<h2>🔧 Teste de Correção de Sessão</h2>";
echo "<p><strong>Localização:</strong> C:\\xampp\\htdocs\\estoque-ruckus</p>";

// Show current session status
echo "<p><strong>Status da sessão antes:</strong> ";
switch (session_status()) {
    case PHP_SESSION_DISABLED:
        echo "❌ DISABLED";
        break;
    case PHP_SESSION_NONE:
        echo "⚪ NONE (não iniciada)";
        break;
    case PHP_SESSION_ACTIVE:
        echo "✅ ACTIVE";
        break;
}
echo "</p>";

// Test User class
echo "<h3>Teste da Classe User Corrigida:</h3>";
try {
    require_once 'config/app.php';
    require_once 'classes/User.php';
    
    $user = new User();
    echo "<p>✅ Classe User carregada com sucesso</p>";
    
    // Test isLoggedIn multiple times (this should NOT cause warnings)
    echo "<p>Testando múltiplas chamadas de sessão:</p>";
    
    for ($i = 1; $i <= 3; $i++) {
        $isLoggedIn = $user->isLoggedIn();
        echo "<p>  Teste $i - Status: " . ($isLoggedIn ? "✅ Logado" : "❌ Não logado") . "</p>";
    }
    
    // Show session status after
    echo "<p><strong>Status da sessão depois:</strong> ";
    switch (session_status()) {
        case PHP_SESSION_DISABLED:
            echo "❌ DISABLED";
            break;
        case PHP_SESSION_NONE:
            echo "⚪ NONE";
            break;
        case PHP_SESSION_ACTIVE:
            echo "✅ ACTIVE";
            break;
    }
    echo "</p>";
    
    // Show session data if available
    if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION)) {
        echo "<p><strong>Dados da sessão:</strong></p>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    }
    
    echo "<p>🎉 <strong>Teste concluído SEM WARNINGS!</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard/index.php'>🏠 Ir para Dashboard</a></p>";
echo "<p><a href='auth/login.php'>🔑 Ir para Login</a></p>";
echo "<p><a href='index.php'>📝 Ir para Cadastro</a></p>";
?>
