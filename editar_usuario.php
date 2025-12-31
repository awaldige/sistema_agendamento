<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: usuarios.php");
    exit();
}

// Busca usuário
$stmt = $conn->prepare("SELECT id, nome, username FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $senha    = trim($_POST['senha'] ?? '');

    if ($nome === '' || $username === '') {
        $erro = 'Nome e username são obrigatórios.';
    } else {

        // Verifica username duplicado
        $check = $conn->prepare("
            SELECT id FROM usuarios 
            WHERE username = :username AND id <> :id
        ");
        $check->execute([
            ':username' => $username,
            ':id' => $id
        ]);

        if ($check->fetch()) {
            $erro = 'Esse username já está em uso.';
        } else {

            if ($senha !== '') {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios 
                        SET nome = :nome, username = :username, senha = :senha
                        WHERE id = :id";
                $params = [
                    ':nome' => $nome,
                    ':username' => $username,
                    ':senha' => $hash,
                    ':id' => $id
                ];
            } else {
                $sql = "UPDATE usuarios 
                        SET nome = :nome, username = :username
                        WHERE id = :id";
                $params = [
                    ':nome' => $nome,
                    ':username' => $username,
                    ':id' => $id
                ];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $sucesso = 'Usuário atualizado com sucesso!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
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
        h2 { text-align: center; }
        label { display: block; margin-top: 12px; }
        input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 14px;
            background: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .erro {
            background: #ffeded;
            padding: 12px;
            border-left: 4px solid #e74c3c;
            margin-bottom: 15px;
        }
        .sucesso {
            background: #eafaf1;
            padding: 12px;
            border-left: 4px solid #2ecc71;
            margin-bottom: 15px;
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

    <h2>Editar Usuário</h2>

    <?php if ($erro): ?><div class="erro"><?= $erro ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="sucesso"><?= $sucesso ?></div><?php endif; ?>

    <form method="post">
        <label>Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>

        <label>Nova Senha (opcional)</label>
        <input type="password" name="senha" placeholder="Deixe em branco para manter">

        <button type="submit">Salvar Alterações</button>
    </form>

</div>

</body>
</html>
