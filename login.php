<?php
session_start();
require_once 'conexao.php'; // Certifique-se que o arquivo tem o código que você me enviou

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($username && $senha) {
        try {
            // No PostgreSQL, usamos ILIKE se quisermos busca insensível a maiúsculas
            $sql = "SELECT id, nome, username, senha FROM usuarios WHERE username = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':username' => $username]);
            $usuario = $stmt->fetch();

            // Verificação de senha
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                session_regenerate_id(true);

                $_SESSION['user_id']   = $usuario['id'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['username']  = $usuario['username'];

                header("Location: index.php");
                exit();
            } else {
                $erro = "Usuário ou senha inválidos.";
            }
        } catch (PDOException $e) {
            // Log do erro para o desenvolvedor, mensagem genérica para o usuário
            error_log($e->getMessage());
            $erro = "Erro de comunicação com o banco de dados.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso ao Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; padding: 0; font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh; display: flex; justify-content: center; align-items: center;
        }
        .login-box {
            background: #fff; padding: 40px; width: 100%; max-width: 400px;
            border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); text-align: center;
        }
        h2 { color: #1e3c72; margin-bottom: 25px; }
        .erro-box {
            background: #fff0f0; color: #d63031; padding: 12px; border-radius: 8px;
            margin-bottom: 20px; font-size: 14px; border: 1px solid #fab1a0;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .input-group { text-align: left; margin-bottom: 15px; }
        .input-field {
            display: flex; align-items: center; background: #f1f3f6;
            padding: 0 15px; border-radius: 10px; border: 2px solid transparent; transition: 0.3s;
        }
        .input-field:focus-within { border-color: #1e3c72; background: #fff; }
        .input-field i { color: #1e3c72; margin-right: 10px; }
        .input-field input {
            border: none; background: transparent; outline: none;
            width: 100%; padding: 12px 0; font-size: 15px;
        }
        .btn-login {
            width: 100%; background: #1e3c72; color: #fff; border: none;
            padding: 15px; font-size: 16px; font-weight: 600; border-radius: 10px;
            cursor: pointer; margin-top: 20px; transition: 0.3s;
        }
        .btn-login:hover { background: #16325c; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Acesso</h2>
        <?php if ($erro): ?>
            <div class="erro-box"><i class="fas fa-exclamation-circle"></i> <?= $erro ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Usuário" required autofocus>
                </div>
            </div>
            <div class="input-group">
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>
