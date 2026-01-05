<?php
session_start();
require_once 'conexao.php'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Limpa os dados vindos do formulário
    $username_input = trim($_POST['username'] ?? '');
    $senha_input    = trim($_POST['senha'] ?? '');

    if ($username_input && $senha_input) {
        try {
            // 2. Busca ignorando maiúsculas e espaços no banco
            $sql = "SELECT id, nome, username, senha FROM usuarios WHERE LOWER(TRIM(username)) = LOWER(:u) LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':u' => $username_input]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // 3. Limpa o hash vindo do banco antes de verificar
                $hash_banco = trim($usuario['senha']);

                if (password_verify($senha_input, $hash_banco)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $usuario['id'];
                    $_SESSION['user_nome'] = $usuario['nome'];
                    header("Location: index.php");
                    exit();
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "O usuário '" . htmlspecialchars($username_input) . "' não existe no banco.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no Banco: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Profissional</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1e3c72, #2a5298); font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 40px; border-radius: 15px; width: 100%; max-width: 380px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); text-align: center; }
        .erro { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fecaca; }
        .input-group { margin-bottom: 15px; text-align: left; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; transition: 0.3s; box-sizing: border-box; }
        input:focus { border-color: #1e3c72; }
        button { width: 100%; padding: 14px; background: #1e3c72; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="color: #1e3c72;">Acesso ao Sistema</h2>
        <?php if ($erro): ?>
            <div class="erro"><i class="fas fa-exclamation-triangle"></i> <?= $erro ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Usuário" required>
            </div>
            <div class="input-group">
                <input type="password" name="senha" placeholder="Senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
