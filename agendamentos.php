<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$filtro = $_GET['filtro'] ?? null;
$titulo = "Agendamentos";
$where  = "";
$params = [];

/* FILTROS */
if ($filtro === 'hoje') {
    $titulo = "Agendamentos de Hoje";
    $where = "WHERE data = :data";
    $params[':data'] = date("Y-m-d");

} elseif ($filtro === 'semana') {
    $titulo = "Agendamentos da Semana";
    $where = "WHERE data BETWEEN :i AND :f";
    $params[':i'] = date("Y-m-d", strtotime("monday this week"));
    $params[':f'] = date("Y-m-d", strtotime("sunday this week"));

} elseif ($filtro === 'mes') {
    $titulo = "Agendamentos do Mês";
    $where = "WHERE data BETWEEN :i AND :f";
    $params[':i'] = date("Y-m-01");
    $params[':f'] = date("Y-m-t");
}

/* BUSCA */
$sql = "
SELECT a.*, s.nome AS servico
FROM agendamentos a
LEFT JOIN servicos s ON s.id = a.servico_id
$where
ORDER BY data, hora
";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= $titulo ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    background:#f4f6fb;
    font-family: Poppins, sans-serif;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    background:#fff;
    padding:30px;
    border-radius:14px;
}
.topo {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.btn {
    padding:10px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
}
.novo {
    background:#4a6cf7;
    color:#fff;
}
.voltar {
    background:#7f8c8d;
    color:#fff;
}
table {
    width:100%;
    border-collapse:collapse;
}
th, td {
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
}
th {
    background:#f0f2f7;
}
</style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h2><?= $titulo ?></h2>

        <?php if (!$filtro): ?>
            <!-- ✅ SÓ NA LISTA GERAL -->
            <a href="novo_agendamento.php" class="btn novo">+ Novo Agendamento</a>
        <?php else: ?>
            <!-- ✅ SÓ QUANDO VEM DO DASHBOARD -->
            <a href="index.php" class="btn voltar">← Voltar</a>
        <?php endif; ?>
    </div>

    <table>
        <tr>
            <th>Paciente</th>
            <th>Serviço</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Tipo</th>
        </tr>

        <?php if (count($agendamentos) === 0): ?>
            <tr>
                <td colspan="5">Nenhum agendamento encontrado.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($agendamentos as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['paciente']) ?></td>
            <td><?= htmlspecialchars($a['servico']) ?></td>
            <td><?= date("d/m/Y", strtotime($a['data'])) ?></td>
            <td><?= $a['hora'] ?></td>
            <td><?= ucfirst($a['tipo_consulta']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
