<?php
session_start();
require_once 'conexao.php'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha    = trim($_POST['senha'] ?? ''); // Trim também na senha

    if ($username && $senha) {
        try {
            // No PostgreSQL, buscamos ignorando maiúsculas/minúsculas
            $sql = "SELECT id, nome, username, senha FROM usuarios WHERE LOWER(username) = LOWER(:username) LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':username' => $username]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Verificação da senha
                if (password_verify($senha, trim($usuario['senha']))) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $usuario['id'];
                    $_SESSION['user_nome'] = $usuario['nome'];
                    header("Location: index.php");
                    exit();
                } else {
                    $erro = "Senha incorreta para este usuário.";
                }
            } else {
                $erro = "O usuário '$username' não foi encontrado.";
            }
        } catch (PDOException $e) {
            $erro = "Erro de conexão: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #1e3c72; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #fff; padding: 40px; border-radius: 12px; width: 350px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .erro { background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fecaca; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1e3c72; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #2a5298; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Entrar</h2>
        <?php if ($erro): ?>
            <div class="erro"><?= $erro ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Usuário (awaldige)" required autofocus>
            <input type="password" name="senha" placeholder="Senha (785143)" required>
            <button type="submit">Acessar</button>
        </form>
    </div>
</body>
</html>
