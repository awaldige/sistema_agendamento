<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Data de hoje
$hoje = date("Y-m-d");

// Buscar agendamentos de hoje
$sql = "
    SELECT 
        a.id,
        a.paciente,
        a.email,
        a.telefone,
        a.data,
        a.hora,
        a.tipo_consulta,
        s.nome AS servico
    FROM agendamentos a
    JOIN servicos s ON a.servico_id = s.id
    WHERE a.data = :hoje
    ORDER BY a.hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':hoje', $hoje);
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamentos de Hoje</title>
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
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            color: #2c3e50;
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e1e6f0;
            text-align: left;
        }

        th {
            background: #f0f3fa;
            font-weight: 600;
        }

        .sem-agendamentos {
            text-align: center;
            padding: 15px;
            color: #555;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="topo">
            <a href="index.php" class="voltar">← Voltar</a>
        </div>

        <h2>Agendamentos de Hoje</h2>

        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Serviço</th>
                    <th>Hora</th>
                    <th>Telefone</th>
                    <th>Tipo</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($agendamentos) === 0): ?>
                    <tr><td colspan="5" class="sem-agendamentos">Nenhum agendamento para hoje.</td></tr>
                <?php else: ?>
                    <?php foreach ($agendamentos as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['paciente']) ?></td>
                            <td><?= htmlspecialchars($a['servico']) ?></td>
                            <td><?= substr($a['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($a['telefone']) ?></td>
                            <td><?= htmlspecialchars($a['tipo_consulta']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

    </div>

</body>

</html>
