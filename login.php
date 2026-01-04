<?php
session_start();
require_once 'conexao.php';

// Segurança: Se já estiver logado, redireciona para a index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($username && $senha) {
        try {
            $sql = "SELECT id, nome, username, senha FROM usuarios WHERE username = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':username' => $username]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Prevenção de Session Fixation
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
            $erro = "Erro no servidor. Tente novamente.";
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
        /* ===== Variáveis e Reset ===== */
        :root {
            --primary: #1e3c72;
            --secondary: #2a5298;
            --accent: #4a6cf7;
            --bg: #f4f7fe;
            --text: #333;
            --white: #ffffff;
            --error-bg: #fff0f0;
            --error-text: #d63031;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text);
        }

        /* ===== Container Login ===== */
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            animation: fadeIn 0.6s ease-out;
        }

        .login-box {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-box h2 {
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
            display: block;
        }

        /* ===== Alerta de Erro ===== */
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
            border: 1px solid #fab1a0;
        }
        .erro-box i { margin-right: 8px; }

        /* ===== Inputs Estilizados ===== */
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: #aaa;
            transition: 0.3s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #eee;
            border-radius: 12px;
            outline: none;
            font-size: 15px;
            transition: 0.3s;
            font-family: inherit;
        }

        .input-wrapper input:focus {
            border-color: var(--accent);
            background: #fff;
        }

        .input-wrapper input:focus + i {
            color: var(--accent);
        }

        /* ===== Botão ===== */
        .btn-login {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #16325c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        /* Animação */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .login-box { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Entrar</h2>
        <span class="subtitle">Bem-vindo de volta! Por favor, acesse sua conta.</span>

        <?php if ($erro): ?>
            <div class="erro-box">
                <i class="fas fa-circle-exclamation"></i>
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Usuário</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="username" placeholder="Seu usuário" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="senha" id="senha" placeholder="Sua senha" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                Acessar Sistema <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>
