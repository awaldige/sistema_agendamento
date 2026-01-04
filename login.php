<?php
session_start();
require_once 'conexao.php';

// 1. SEGURANÇA: Se o usuário já estiver logado, manda direto para a index
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
            // 2. SEGURANÇA: SQL Injection protegida via Prepared Statements
            $sql = "SELECT id, nome, username, senha FROM usuarios WHERE username = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':username' => $username]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // 3. SEGURANÇA: password_verify é essencial para senhas com Hash
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                
                // 4. SEGURANÇA: Gera novo ID de sessão para evitar sequestro de conta
                session_regenerate_id(true);

                $_SESSION['user_id']   = $usuario['id'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['username']  = $usuario['username'];
                $_SESSION['logado_em'] = date('Y-m-d H:i:s');

                header("Location: index.php");
                exit();

            } else {
                // Mensagem genérica para não revelar se o usuário existe ou não (Segurança)
                $erro = "Usuário ou senha incorretos.";
            }

        } catch (PDOException $e) {
            $erro = "Ocorreu um erro técnico. Tente novamente mais tarde.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
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
    <style>
        /* ===== Design Profissional ===== */
        :root {
            --primary: #1e3c72;
            --accent: #4a6cf7;
            --error: #e74c3c;
            --text: #333;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 { color: var(--primary); margin-bottom: 10px; }
        p.subtitle { color: #666; margin-bottom: 25px; font-size: 14px; }

        /* Estilo da Caixa de Erro */
        .erro-alerta {
            background: #fdf2f2;
            color: var(--error);
            border: 1px solid #f8d7da;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            animation: shake 0.4s ease-in-out;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 40px;
            color: #aaa;
        }

        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: var(--primary); }

        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 8px rgba(74, 108, 247, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover { background: #16325c; transform: translateY(-1px); }

        /* Animação para erro */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Acesso</h2>
    <p class="subtitle">Entre com suas credenciais</p>

    <?php if ($erro): ?>
        <div class="erro-alerta">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="input-group">
            <label for="username">Usuário</label>
            <i class="fas fa-user"></i>
            <input type="text" name="username" id="username" placeholder="Nome de usuário" required autofocus>
        </div>

        <div class="input-group">
            <label for="senha">Senha</label>
            <i class="fas fa-lock"></i>
            <input type="password" name="senha" id="senha" placeholder="Sua senha" required>
        </div>

        <button type="submit">Entrar no Sistema</button>
    </form>
</div>

</body>
</html>
