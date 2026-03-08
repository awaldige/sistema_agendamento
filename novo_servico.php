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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Serviço</title>

    <link rel="icon" href="agendamento_medico.jpg" type="image/jpeg">

    <style>
        body {
            background-color: #f3f6fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width:500px;
            margin:60px auto;
            background:#fff;
            padding:30px;
            border-radius:16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            color: #2c3e50;
        }
        input, textarea {
            width:100%;
            padding:12px;
            margin-bottom:15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* Importante para o padding não quebrar a largura */
        }
        .btn {
            padding:12px 20px;
            border-radius:8px;
            text-decoration:none;
            color:#fff;
            background:#4a6cf7;
            border: none;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
        }
        .btn:hover {
            background: #3b5be0;
        }
        .menu {
            background:#7f8c8d;
            margin-left: 10px;
        }
        .menu:hover {
            background: #6c7a7b;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Novo Serviço</h2>

    <form method="post">
        <input type="text" name="nome" placeholder="Nome do serviço" required>
        <textarea name="descricao" placeholder="Descrição" rows="4"></textarea>

        <div style="margin-top: 10px;">
            <button class="btn" type="submit">Salvar Serviço</button>
            <a href="servicos.php" class="btn menu">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>
