<?php
session_start();
require_once "conexao.php";

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$inicioSemana = date('Y-m-d', strtotime('monday this week'));
$fimSemana    = date('Y-m-d', strtotime('sunday this week'));

$sql = "
    SELECT 
        a.id,
        a.paciente,
        a.telefone,
        a.data,
        a.hora,
        a.tipo_consulta,
        s.nome AS servico
    FROM agendamentos a
    LEFT JOIN servicos s ON s.id = a.servico_id
    WHERE a.data BETWEEN ? AND ?
    ORDER BY a.data ASC, a.hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute([$inicioSemana, $fimSemana]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamentos da Semana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .voltar {
            padding: 10px 15px;
            background: #7f8c8d;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
        }

        .voltar:hover {
            background: #636e72;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e1e6f0;
        }

        th {
            background: #f0f3fa;
        }

        .vazio {
            text-align: center;
            padding: 20px;
            color: #777;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="topo">
            <a href="index.php" class="voltar">← Voltar</a>
            <h2>Agendamentos da Semana</h2>
            <div></div>
        </div>

        <?php if (count($agendamentos) === 0): ?>
            <p class="vazio">Nenhum agendamento encontrado nesta semana.</p>
        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Serviço</th>
                        <th>Telefone</th>
                        <th>Consulta</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($agendamentos as $a): ?>
                        <tr>
                            <td><?= date("d/m/Y", strtotime($a['data'])) ?></td>
                            <td><?= substr($a['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($a['paciente']) ?></td>
                            <td><?= htmlspecialchars($a['servico']) ?></td>
                            <td><?= htmlspecialchars($a['telefone']) ?></td>
                            <td><?= htmlspecialchars($a['tipo_consulta']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        <?php endif; ?>

    </div>

</body>

</html>
