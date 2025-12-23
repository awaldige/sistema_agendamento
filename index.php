<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* 🔹 GRÁFICO MÊS / ANO */
$stmt = $conn->query("
    SELECT
        EXTRACT(YEAR FROM data) AS ano,
        EXTRACT(MONTH FROM data) AS mes,
        COUNT(*) AS total
    FROM agendamentos
    GROUP BY ano, mes
    ORDER BY ano, mes
");

$labels = [];
$totais = [];
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $labels[] = $r['mes'].'/'.$r['ano'];
    $totais[] = $r['total'];
}

/* 🔹 PARTICULAR x CONVÊNIO */
$tipos = $conn->query("
    SELECT tipo_consulta, COUNT(*) total
    FROM agendamentos
    GROUP BY tipo_consulta
")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<aside class="sidebar">
<ul class="menu">
<li><a class="active" href="index.php">📊 Dashboard</a></li>
<li><a href="agendamentos.php">📅 Consultar Consultas</a></li>
<li><a href="novo_agendamento.php">➕ Novo Agendamento</a></li>
<li><a href="logout.php">🚪 Sair</a></li>
</ul>
</aside>

<main class="main-content">

<header>
    <button class="toggle-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
    <div class="user-info">Bem-vindo</div>
</header>

<div class="dashboard-graficos">

<div class="grafico-box">
    <h4>Consultas por Mês/Ano</h4>
    <canvas id="graficoMes"></canvas>
</div>

<div class="grafico-box">
    <h4>Particular x Convênio</h4>
    <canvas id="graficoTipo"></canvas>
</div>

</div>

</main>

<script>
new Chart(document.getElementById('graficoMes'), {
    type:'bar',
    data:{
        labels:<?= json_encode($labels) ?>,
        datasets:[{
            label:'Consultas',
            data:<?= json_encode($totais) ?>
        }]
    }
});

new Chart(document.getElementById('graficoTipo'), {
    type:'doughnut',
    data:{
        labels:<?= json_encode(array_keys($tipos)) ?>,
        datasets:[{
            data:<?= json_encode(array_values($tipos)) ?>
        }]
    }
});
</script>

</body>
</html>
