<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estoque Ruckus - Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-wifi"></i> Sistema de Estoque Ruckus</h3>
                        <p class="mb-0">Cadastro de Usuário</p>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        // Display messages
                        if (isset($_GET['message'])) {
                            $messageType = isset($_GET['type']) && $_GET['type'] === 'success' ? 'success' : 'danger';
                            echo "<div class='alert alert-$messageType'>" . htmlspecialchars($_GET['message']) . "</div>";
                        }
                        ?>
                        
                        <form action="auth/register.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Nome Completo
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> E-mail
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Senha
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmar Senha
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus"></i> Cadastrar
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <p class="mb-0">Já possui conta?</p>
                            <a href="auth/login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt"></i> Fazer Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validate password confirmation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
