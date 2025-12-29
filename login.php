<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($username === '' || $senha === '') {
        $erro = 'Preencha usuário e senha.';
    } else {

        $sql = "SELECT id, nome, username, senha
                FROM usuarios
                WHERE username = :username
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistema</title>

<link rel="stylesheet" href="login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">

        <h2><i class="fas fa-user-shield"></i> Acesso Restrito</h2>

        <?php if ($erro): ?>
            <div class="erro-box"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post">

            <label>Usuário</label>
            <input type="text" name="username" required>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>

        </form>
    </div>
</div>

</body>
</html>
