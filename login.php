<?php
session_start();
require_once 'conexao.php'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = trim($_POST['username'] ?? '');
    $senha_input    = trim($_POST['senha'] ?? '');

    if ($username_input && $senha_input) {
        try {
            $sql = "SELECT id, nome, username, senha, nivel FROM usuarios WHERE LOWER(TRIM(username)) = LOWER(:u) LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':u' => $username_input]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                if (password_verify($senha_input, trim($usuario['senha']))) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']    = $usuario['id'];
                    $_SESSION['user_nome']  = $usuario['nome'];
                    $_SESSION['nivel']      = $usuario['nivel']; 
                    header("Location: index.php");
                    exit();
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "Usuário não encontrado.";
            }
        } catch (PDOException $e) {
            $erro = "Erro técnico no sistema.";
        }
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
        /* Variáveis para facilitar personalização */
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --error-bg: #fee2e2;
            --error-text: #b91c1c;
            --input-bg: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh; /* Importante para mobile */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-box {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
        }

        .login-box h2 {
            margin: 0 0 10px;
            font-size: 24px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 30px;
            font-size: 14px;
        }

        /* Alerta de Erro */
        .erro-box {
            background: var(--error-bg);
            color: var(--error-text);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #fecaca;
        }

        /* Formulário e Inputs */
        form { text-align: left; }

        .input-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            margin-left: 4px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            background: var(--input-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            outline: none;
            font-size: 15px;
            color: #1e293b;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: var(--secondary-color);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
        }

        .input-group input:focus + i {
            color: var(--secondary-color);
        }

        /* Botão */
        .btn-login {
            width: 100%;
            background: var(--primary-color);
            color: #fff;
            border: none;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        .btn-login:active { transform: translateY(0); }

        /* Responsividade para celulares bem pequenos */
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
            }
            .login-box h2 { font-size: 20px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h2>Bem-vindo</h2>
            <p class="subtitle">Faça login para gerenciar o sistema</p>

            <?php if ($erro): ?>
                <div class="erro-box">
                    <i class="fas fa-circle-exclamation"></i>
                    <?= $erro ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <span class="input-label">Usuário</span>
                <div class="input-group">
                    <input type="text" name="username" placeholder="Digite seu usuário" required>
                    <i class="fas fa-user"></i>
                </div>

                <span class="input-label">Senha</span>
                <div class="input-group">
                    <input type="password" name="senha" placeholder="••••••••" required>
                    <i class="fas fa-lock"></i>
                </div>

                <button type="submit" class="btn-login">
                    Entrar no Sistema
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
