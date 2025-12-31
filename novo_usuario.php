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

    if ($nome === '' || $username === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {

        // Verifica username duplicado
        $check = $conn->prepare(
            "SELECT id FROM usuarios WHERE username = :username LIMIT 1"
        );
        $check->execute([':username' => $username]);

        if ($check->fetch()) {
            $erro = 'Esse username já está em uso.';
        } else {

            // Gera hash seguro
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, username, senha)
                    VALUES (:nome, :username, :senha)";

            $stmt = $conn->prepare($sql);

            if ($stmt->execute([
                ':nome' => $nome,
                ':username' => $username,
                ':senha' => $hash
            ])) {
                header("Location: usuarios.php?sucesso=1");
                exit();
            } else {
                $erro = 'Erro ao criar usuário.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 12px;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #f8f9fc;
        }
        button {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #2649f5;
        }
        .erro {
            background: #ffeded;
            padding: 12px;
            border-left: 4px solid #e74c3c;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .voltar {
            display: inline-block;
            margin-bottom: 15px;
            text-decoration: none;
            color: #555;
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

        <button type="submit">Salvar Usuário</button>

    </form>

</div>

</body>
</html>
