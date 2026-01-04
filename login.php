<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($username === '' || $senha === '') {
        $erro = 'Preencha o usuário e a senha.';
    } else {

        $sql = "
            SELECT id, nome, username, senha
            FROM usuarios
            WHERE username = :username
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
       
        
        if ($user && password_verify($senha, $user['senha'])) {

            // LOGIN OK
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];

            header('Location: index.php');
            exit;

        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - Sistema de Agendamentos</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

        <form method="post">

            <label>Usuário</label>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" required autofocus>
            </div>

            <label>Senha</label>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" required>
            </div>

            <button type="submit" class="btn-login">
                Entrar <i class="fas fa-arrow-right"></i>
            </button>

        </form>

    </div>
</div>
