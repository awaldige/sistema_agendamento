<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o email e a senha.';
    } else {

        $sql = "SELECT id, nome, email, senha, nivel
                FROM usuarios
                WHERE email = :email
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {

            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nome']  = $user['nome'];
            $_SESSION['user_nivel'] = $user['nivel'];

            header('Location: index.php');
            exit;

        } else {
            $erro = 'Email ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Agendamentos</title>

    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        
        <h2><i class="fas fa-user-shield"></i> Acesso Restrito</h2>
        <p class="subtitle">Entre para continuar</p>

        <?php if ($erro): ?>
            <div class="erro-box">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">

            <label>Email</label>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input
                    type="email"
                    name="email"
                    placeholder="Digite seu email"
                    required
                >
            </div>

            <label>Senha</label>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input
                    type="password"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                >
            </div>

            <button type="submit" class="btn-login">
                Entrar <i class="fas fa-arrow-right"></i>
            </button>
        </form>

    </div>
</div>

</body>
</html>
