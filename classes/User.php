<?php
/**
 * User Class
 * Handles user authentication and management
 */

require_once '../config/database.php';

class User {
    private $pdo;
    private static $sessionStarted = false;
    
    public function __construct() {
        $this->pdo = getConnection();
    }
    
    /**
     * Start session safely (only if not already started)
     */
    private function ensureSession() {
        if (!self::$sessionStarted && session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0);
            
            session_start();
            self::$sessionStarted = true;
        }
    }
    
    /**
     * Register a new user
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string $password User's password (will be hashed)
     * @return array Result with success status and message
     */
    public function register($name, $email, $password) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'E-mail já está cadastrado'];
            }
            
            // Hash password and insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);
            
            return ['success' => true, 'message' => 'Usuário cadastrado com sucesso'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $e->getMessage()];
        }
    }
    
    /**
     * Authenticate user login
     * @param string $email User's email
     * @param string $password User's password
     * @return array Result with success status and user data
     */
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Start session and store user data
                $this->ensureSession();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                return [
                    'success' => true, 
                    'message' => 'Login realizado com sucesso',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'E-mail ou senha incorretos'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao fazer login: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if user is logged in
     * @return boolean True if user is authenticated
     */
    public function isLoggedIn() {
        $this->ensureSession();
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        $this->ensureSession();
        session_destroy();
        self::$sessionStarted = false;
    }
    
    /**
     * Get current user data
     * @return array|null User data or null if not logged in
     */
    public function getCurrentUser() {
        $this->ensureSession();
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ];
        }
        return null;
    }
}
?>
