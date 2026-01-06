<?php
session_start();
require_once 'conexao.php';
require_once 'verifica_admin.php'; // Apenas admins criam usuários

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $user     = trim($_POST['username']);
    $senha    = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $nivel    = $_POST['nivel']; // Recebe 'admin' ou 'colaborador'

    try {
        $sql = "INSERT INTO usuarios (nome, username, senha, nivel) VALUES (:nome, :user, :senha, :nivel)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nome'  => $nome,
            ':user'  => $user,
            ':senha' => $senha,
            ':nivel' => $nivel
        ]);
        header("Location: usuarios.php?sucesso=1");
        exit;
    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#eef2f7; font-family: sans-serif; padding: 20px; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { color: #1e3c72; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px; }
        .btn-salvar { background: #2ecc71; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; }
        .btn-voltar { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
        .erro { background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-user-plus"></i> Novo Usuário</h2>
    
    <?php if ($mensagem): ?>
        <div class="erro"><?= $mensagem ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" placeholder="Ex: João Silva" required>
        </div>

        <div class="form-group">
            <label>Nome de Usuário (Login)</label>
            <input type="text" name="username" placeholder="Ex: joao.silva" required>
        </div>

        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label>Nível de Acesso</label>
            <select name="nivel" required>
                <option value="colaborador">Colaborador (Acesso Limitado)</option>
                <option value="admin">Administrador (Acesso Total)</option>
            </select>
        </div>

        <button type="submit" class="btn-salvar">Criar Usuário</button>
        <a href="usuarios.php" class="btn-voltar">Cancelar e Voltar</a>
    </form>
</div>

</body>
</html>
