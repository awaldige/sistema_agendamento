<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $senha    = trim($_POST['senha'] ?? '');
    $nivel    = $_POST['nivel'] ?? 'colaborador';

    if ($nome === '' || $username === '' || $senha === '') {
        $erro = 'Preencha nome, username e senha.';
    } else {

        // Verifica username duplicado
        $check = $conn->prepare("SELECT id FROM usuarios WHERE username = :username LIMIT 1");
        $check->execute([':username' => $username]);
        
        if ($check->fetch()) {
            $erro = 'Esse username já está em uso.';
        } else {

            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, username, senha, nivel)
                    VALUES (:nome, :username, :senha, :nivel)";

            $stmt = $conn->prepare($sql);

            if ($stmt->execute([
                ':nome' => $nome,
                ':username' => $username,
                ':senha' => $hash,
                ':nivel' => $nivel
            ])) {
                header("Location: usuarios.php?sucesso=1");
                exit();
            } else {
                $erro = 'Erro ao salvar usuário.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Novo Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #2c3e50;
        }

        .voltar {
            display: inline-block;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #7f8c8d;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
        }

        .voltar:hover {
            background: #636e72;
        }

        label {
            font-weight: 500;
            display: block;
            margin-top: 12px;
            color: #2c3e50;
        }

        input, select {
            width: 100%;
            margin-top: 5px;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dfe6ed;
            background: #f8f9fc;
            font-size: 15px;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 14px;
            background: #4a6cf7;
            border: none;
            color: #fff;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.25s;
        }

        button:hover {
            background: #2649f5;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(74,108,247,0.25);
        }

        .erro {
            background: #ffeded;
            padding: 12px;
            border-left: 4px solid #e74c3c;
            color: #c0392b;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="usuarios.php" class="voltar">← Voltar</a>

    <h2>Cadastrar Novo Usuário</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">

        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Senha</label>
        <input type="password" name="senha" required>

        <label>Nível</label>
        <select name="nivel">
            <option value="admin">Administrador</option>
            <option value="colaborador" selected>Colaborador</option>
        </select>

        <button type="submit">Salvar Usuário</button>

    </form>

</div>

</body>
</html>
