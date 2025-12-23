<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* =========================
   GRÁFICO – CONSULTAS POR MÊS
========================= */
$grafMes = $conn->query("
    SELECT 
        TO_CHAR(data, 'MM/YYYY') as mes,
        COUNT(*) as total
    FROM agendamentos
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GRÁFICO – TIPO DE CONSULTA
========================= */
$grafTipo = $conn->query("
    SELECT tipo_consulta, COUNT(*) as total
    FROM agendamentos
    GROUP BY tipo_consulta
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<aside class="sidebar">
    <ul class="menu">
        <li>
            <a href="index.php" class="active">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="agendamentos.php">
                <i class="fas fa-calendar-check"></i> Consultar Consultas
            </a>
        </li>

        <li>
            <a href="novo_agendamento.php">
                <i class="fas fa-plus-circle"></i> Novo Agendamento
            </a>
        </li>

        <li>
            <a href="servicos.php">
                <i class="fas fa-briefcase"></i> Serviços
            </a>
        </li>

        <li>
            <a href="usuarios.php">
                <i class="fas fa-users"></i> Usuários
            </a>
        </li>

        <li class="logout-box">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </li>
    </ul>
</aside>

<main class="main-content">

<header>
    <h1>Dashboard</h1>
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <?= htmlspecialchars($nomeUsuario) ?>
    </div>
</header>

<section class="content-box">
    <h2>Visão Geral</h2>

    <div class="dashboard-graficos">

        <div class="grafico-box">
            <h3>Consultas por Mês</h3>
            <canvas id="graficoMes"></canvas>
        </div>

        <div class="grafico-box">
            <h3>Tipo de Consulta</h3>
            <canvas id="graficoTipo"></canvas>
        </div>

    </div>
</section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const meses = <?= json_encode(array_column($grafMes, 'mes')) ?>;
const totalMes = <?= json_encode(array_column($grafMes, 'total')) ?>;

const tipos = <?= json_encode(array_column($grafTipo, 'tipo_consulta')) ?>;
const totalTipos = <?= json_encode(array_column($grafTipo, 'total')) ?>;

new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [{
            label: 'Consultas',
            data: totalMes,
            backgroundColor: '#4a6cf7'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

new Chart(document.getElementById('graficoTipo'), {
    type: 'doughnut',
    data: {
        labels: tipos,
        datasets: [{
            data: totalTipos,
            backgroundColor: ['#2ecc71', '#f39c12']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>
