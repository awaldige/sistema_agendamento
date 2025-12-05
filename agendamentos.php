<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamentos</title>
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
            margin: 80px auto;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        h2 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .voltar {
            display: inline-block;
            padding: 10px 15px;
            background: #7f8c8d;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 30px;
        }

        .voltar:hover {
            background: #636e72;
        }

        .btn-novo {
            display: inline-block;
            padding: 15px 25px;
            background: #4a6cf7;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
        }

        .btn-novo:hover {
            background: #2649f5;
        }
    </style>
</head>

<body>

    <div class="container">

        <a href="index.php" class="voltar">← Voltar</a>

        <h2>Agendamentos</h2>

        <a href="novo_agendamento.php" class="btn-novo">+ Novo Agendamento</a>

    </div>

</body>

</html>
