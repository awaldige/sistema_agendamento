<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Buscar usuários (SEM email)
$sql = "SELECT id, nome, username, nivel FROM usuarios ORDER BY nome ASC";
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Usuários</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>

body {
    margin: 0;
    padding: 0;
    background: #f4f6fb;
    font-family: "Poppins", sans-serif;
}

.container {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
    font-weight: 600;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 14px;
    border-bottom: 1px solid #e1e6f0;
}

th {
    background: #f0f3fa;
    font-weight: 600;
}

.btn {
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    font-size: 13px;
}

.btn-add {
    background: #27ae60;
    float: right;
    margin-bottom: 20px;
}

.btn-add:hover {
    background: #1e8449;
}

.btn-del {
    background: #e74c3c;
}

.btn-del:hover {
    background: #c0392b;
}

.btn-edit {
    background: #4a6cf7;
}

.btn-edit:hover {
    background: #2649f5;
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

</style>
</head>
<body>

<div class="container">

    <a href="index.php" class="voltar">← Voltar</a>

    <a href="adicionar_usuario.php" class="btn btn-add">+ Novo Usuário</a>

    <h2>Usuários</h2>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Nível</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['nivel']) ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-edit">Editar</a>
                    <a href="excluir_usuario.php?id=<?= $u['id'] ?>" class="btn btn-del" onclick="return confirm('Excluir usuário?');">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
