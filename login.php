<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($username === '' || $senha === '') {
        $erro = "Preencha usuário e senha.";
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
            $_SESSION['username']  = $user['username'];

            header("Location: index.php");
            exit();

        } else {
            $erro = "Usuário ou senha inválidos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
    background:#eef2f7;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Poppins,sans-serif;
}
.box{
    background:#fff;
    padding:30px;
    width:320px;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.1);
}
h2{text-align:center;margin-bottom:20px;}
input{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border-radius:8px;
    border:1px solid #ddd;
}
button{
    width:100%;
    padding:12px;
    background:#4a6cf7;
    color:#fff;
    border:none;
    border-radius:8px;
    font-weight:600;
}
.erro{
    background:#fdecea;
    color:#c0392b;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
    text-align:center;
}
</style>
</head>

<body>

<div class="box">
    <h2>Login</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button>Entrar</button>
    </form>
</div>

</body>
</html>
