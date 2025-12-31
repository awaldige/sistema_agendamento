<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    if ($nome) {
        $sql = "INSERT INTO servicos (nome, descricao) VALUES (:n, :d)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':n' => $nome,
            ':d' => $descricao
        ]);
        header("Location: servicos.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Serviço</title>
<style>
.container {
    max-width:500px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:16px;
}
input, textarea {
    width:100%;
    padding:12px;
    margin-bottom:15px;
}
.btn {
    padding:10px 15px;
    border-radius:8px;
    text-decoration:none;
    color:#fff;
    background:#4a6cf7;
}
.menu {
    background:#7f8c8d;
}
</style>
</head>
<body>

<div class="container">
<h2>Novo Serviço</h2>

<form method="post">
<input type="text" name="nome" placeholder="Nome do serviço" required>
<textarea name="descricao" placeholder="Descrição"></textarea>

<button class="btn" type="submit">Salvar</button>
<a href="servicos.php" class="btn menu">Cancelar</a>
</form>
</div>

</body>
</html>
