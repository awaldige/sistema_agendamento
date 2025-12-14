<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* =============================
   CONTROLE DE FILTRO
============================= */
$filtro = $_GET['filtro'] ?? null;
$mostrarLista = false;
$agendamentos = [];

if ($filtro) {
    $mostrarLista = true;

    switch ($filtro) {

        case 'hoje':
            $sql = "SELECT * FROM agendamentos WHERE data = CURDATE() ORDER BY data ASC";
            break;

        case 'semana':
            $sql = "SELECT * FROM agendamentos
                    WHERE data BETWEEN
                    DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                    AND DATE_ADD(CURDATE(), INTERVAL (6 - WEEKDAY(CURDATE())) DAY)
                    ORDER BY data ASC";
            break;

        case 'mes':
            $sql = "SELECT * FROM agendamentos
                    WHERE MONTH(data) = MONTH(CURDATE())
                    AND YEAR(data) = YEAR(CURDATE())
                    ORDER BY data ASC";
            break;

        default:
            $sql = "SELECT * FROM agendamentos ORDER BY data ASC";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            max-width: 800px;
            margin: 80px auto;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
        }

        .voltar {
            display: inline-block;
            padding: 10px 15px;
            background: #7f8c8d;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 25px;
        }

        .voltar:hover {
            background: #636e72;
        }

        .btn-novo {
            display: inline-block;
            padding: 12px 20px;
            background: #4a6cf7;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .btn-novo:hover {
            background: #2649f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background: #f4f6f9;
            color: #333;
        }

        .vazio {
            text-align: center;
            color: #999;
            padding: 20px;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="index.php" class="voltar">← Voltar</a>

    <?php if ($mostrarLista): ?>

        <h2>Agendamentos</h2>

        <?php if (count($agendamentos) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['cliente']) ?></td>
                            <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
                            <td><?= htmlspecialchars($a['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="vazio">Nenhum agendamento encontrado.</p>
        <?php endif; ?>

        <br>
        <a href="agendamentos.php" class="btn-novo">+ Novo Agendamento</a>

    <?php else: ?>

        <h2>Agendamentos</h2>

        <div style="text-align:center;">
            <a href="novo_agendamento.php" class="btn-novo">+ Novo Agendamento</a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
