<?php
session_start();
require_once 'conexao.php';
require_once 'verifica_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: usuarios.php");
    exit;
}

$mensagem = '';

// 1. Busca os dados atuais do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) {
    die("Usuário não encontrado.");
}

// 2. Processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $user  = trim($_POST['username']);
    $nivel = $_POST['nivel'];
    $senha = $_POST['senha']; // Opcional

    try {
        if (!empty($senha)) {
            // Se preencheu senha, atualiza tudo incluindo a nova senha
            $novaSenhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = :nome, username = :user, nivel = :nivel, senha = :senha WHERE id = :id";
            $params = [':nome' => $nome, ':user' => $user, ':nivel' => $nivel, ':senha' => $novaSenhaHash, ':id' => $id];
        } else {
            // Se deixou senha vazia, atualiza apenas os outros dados
            $sql = "UPDATE usuarios SET nome = :nome, username = :user, nivel = :nivel WHERE id = :id";
            $params = [':nome' => $nome, ':user' => $user, ':nivel' => $nivel, ':id' => $id];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        header("Location: usuarios.php?editado=1");
        exit;
    } catch (PDOException $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#eef2f7; font-family: sans-serif; padding: 20px; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { color: #1e3c72; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px; }
        .btn-atualizar { background: #3498db; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; }
        .btn-voltar { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
        .info-senha { font-size: 12px; color: #888; margin-top: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-user-edit"></i> Editar Usuário</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($u['nome']) ?>" required>
        </div>

        <div class="form-group">
            <label>Nome de Usuário</label>
            <input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" required>
        </div>

        <div class="form-group">
            <label>Nível de Acesso</label>
            <select name="nivel" required>
                <option value="colaborador" <?= $u['nivel'] == 'colaborador' ? 'selected' : '' ?>>Colaborador</option>
                <option value="admin" <?= $u['nivel'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nova Senha</label>
            <input type="password" name="senha" placeholder="Deixe em branco para não alterar">
            <p class="info-senha">Preencha apenas se desejar trocar a senha do usuário.</p>
        </div>

        <button type="submit" class="btn-atualizar">Salvar Alterações</button>
        <a href="usuarios.php" class="btn-voltar">Cancelar e Voltar</a>
    </form>
</div>

</body>
</html>
