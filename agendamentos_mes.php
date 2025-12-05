<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Recebe mês/ano da URL ou usa o atual
$mes = isset($_GET['mes']) ? (int) $_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');

// Corrige limites
if ($mes < 1 || $mes > 12) $mes = date('m');
if ($ano < 2000 || $ano > 2100) $ano = date('Y');

// Primeiro e último dia do mês selecionado
$inicioMes = date("$ano-$mes-01");
$fimMes = date("Y-m-t", strtotime($inicioMes));

// Buscar agendamentos com JOIN
$sql = "
    SELECT 
        a.id,
        a.paciente,
        a.email,
        a.telefone,
        a.data,
        a.hora,
        a.tipo_consulta,
        a.observacoes,
        s.nome AS servico
    FROM agendamentos a
    LEFT JOIN servicos s ON s.id = a.servico_id
    WHERE a.data BETWEEN :inicio AND :fim
    ORDER BY a.data ASC, a.hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute([':inicio' => $inicioMes, ':fim' => $fimMes]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Título
$tituloPagina = "Agendamentos de " . sprintf("%02d/%04d", $mes, $ano);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tituloPagina) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0; padding: 0;
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #2c3e50;
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
        .voltar:hover { background: #636e72; }

        .filtro {
            display: flex;
            gap: 10px;
        }
        select, button {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            background: #3498db;
            color: #fff;
            cursor: pointer;
        }
        button:hover { background: #2980b9; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #e1e6f0;
        }
        th { background: #f0f3fa; }
        .vazio { text-align:center; padding:20px; }
    </style>
</head>
<body>
    <div class="container">

        <div class="topo">
            <a href="index.php" class="voltar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>

            <h2><?= htmlspecialchars($tituloPagina) ?></h2>

            <!-- SELETOR DE MÊS / ANO -->
            <form method="GET" class="filtro">
                <select name="mes">
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $sel = ($m == $mes) ? "selected" : "";
                        echo "<option value='$m' $sel>" . sprintf("%02d", $m) . "</option>";
                    }
                    ?>
                </select>

                <select name="ano">
                    <?php
                    $anoAtual = date('Y');
                    for ($a = $anoAtual - 5; $a <= $anoAtual + 5; $a++) {
                        $sel = ($a == $ano) ? "selected" : "";
                        echo "<option value='$a' $sel>$a</option>";
                    }
                    ?>
                </select>

                <button type="submit">Filtrar</button>
            </form>
        </div>

        <?php if (count($agendamentos) == 0): ?>
            <p class="vazio">Nenhum agendamento encontrado.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Serviço</th>
                        <th>Telefone</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $a): ?>
                        <tr>
                            <td><?= date("d/m/Y", strtotime($a['data'])) ?></td>
                            <td><?= substr($a['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($a['paciente']) ?></td>
                            <td><?= htmlspecialchars($a['servico'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($a['telefone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($a['tipo_consulta']) ?></td>
                            <td>
                                <a class="editar" href="editar_agendamento.php?id=<?= $a['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="excluir" 
                                   onclick="return confirm('Excluir?')"
                                   href="excluir_agendamento.php?id=<?= $a['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</body>
</html>
