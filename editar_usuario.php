<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die('ID inválido.');
}

// Buscar usuário
$stmt = $conn->prepare("SELECT id, nome, username, nivel FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Usuário não encontrado.');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim($_POST['nome'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $senha    = trim($_POST['senha'] ?? '');
    $nivel    = $_POST['nivel'] ?? 'colaborador';

    if ($nome === '' || $username === '') {
        $erro = 'Nome e username são obrigatórios.';
    } else {

        // Verifica username duplicado
        $check = $conn->prepare("SELECT id FROM usuarios WHERE username = :username AND id != :id LIMIT 1");
        $check->execute([':username' => $username, ':id' => $id]);

        if ($check->fetch()) {
            $erro = 'Este username já está em uso.';
        } else {

            if ($senha !== '') {
                $hash = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "UPDATE usuarios 
                        SET nome=:nome, username=:username, senha=:senha, nivel=:nivel 
                        WHERE id=:id";

                $params = [
                    ':nome'=>$nome,
                    ':username'=>$username,
                    ':senha'=>$hash,
                    ':nivel'=>$nivel,
                    ':id'=>$id
                ];

            } else {
                $sql = "UPDATE usuarios 
                        SET nome=:nome, username=:username, nivel=:nivel 
                        WHERE id=:id";

                $params = [
                    ':nome'=>$nome,
                    ':username'=>$username,
                    ':nivel'=>$nivel,
                    ':id'=>$id
                ];
            }

            $stmt = $conn->prepare($sql);

            if ($stmt->execute($params)) {
                header("Location: usuarios.php?editado=1");
                exit();
            } else {
                $erro = 'Erro ao atualizar usuário.';
            }
        }
    }

    // Recarrega usuário
    $stmt = $conn->prepare("SELECT id, nome, username, nivel FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
            margin-top: 10px;
            display: block;
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
            margin-top: 20px;
            padding: 12px;
            background: #4a6cf7;
            border: none;
            color: #fff;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #2649f5;
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

    <h2>Editar Usuário</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">

        <label>Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Senha (deixe em branco para manter a atual)</label>
        <input type="password" name="senha">

        <label>Nível</label>
        <select name="nivel">
            <option value="admin" <?= $user['nivel'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            <option value="colaborador" <?= $user['nivel'] === 'colaborador' ? 'selected' : '' ?>>Colaborador</option>
        </select>

        <button type="submit">Salvar Alterações</button>

    </form>

</div>

</body>
</html>

