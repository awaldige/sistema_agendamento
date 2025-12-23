<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* ===== GRÁFICO AGENDAMENTOS POR MÊS ===== */
$stmtMes = $conn->query("
    SELECT EXTRACT(MONTH FROM data) AS mes, COUNT(*) AS total
    FROM agendamentos
    GROUP BY mes
    ORDER BY mes
");
$meses = array_fill(1, 12, 0);
while ($row = $stmtMes->fetch(PDO::FETCH_ASSOC)) {
    $meses[(int)$row['mes']] = (int)$row['total'];
}

/* ===== GRÁFICO TIPO DE CONSULTA ===== */
$stmtTipo = $conn->query("
    SELECT tipo_consulta, COUNT(*) AS total
    FROM agendamentos
    GROUP BY tipo_consulta
");
$tipos = $stmtTipo->fetchAll(PDO::FETCH_KEY_PAIR);

$particular = $tipos['particular'] ?? 0;
$convenio   = $tipos['convenio'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<header class="topbar-mobile">
    <button class="toggle-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>
    <span>Dashboard</span>
</header>

<aside class="sidebar">
    <ul class="menu">
        <li><a href="index.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="agendamentos.php"><i class="fas fa-calendar-check"></i> Consultar Consultas</a></li>
        <li><a href="novo_agendamento.php"><i class="fas fa-plus-circle"></i> Novo Agendamento</a></li>
        <li><a href="servicos.php"><i class="fas fa-briefcase"></i> Serviços</a></li>
        <li><a href="usuarios.php"><i class="fas fa-users"></i> Usuários</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
    </ul>
</aside>

<main class="main-content">

<header>
    <h2>Bem-vindo, <?= htmlspecialchars($nomeUsuario) ?></h2>
</header>

<section class="dashboard-graficos">

    <div class="grafico-box">
        <h3>Agendamentos por Mês</h3>
        <canvas id="graficoMes"></canvas>
    </div>

    <div class="grafico-box">
        <h3>Tipo de Consulta</h3>
        <canvas id="graficoTipo"></canvas>
    </div>

</section>

</main>

<script>
new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        datasets: [{
            label: 'Agendamentos',
            data: <?= json_encode(array_values($meses)) ?>,
            backgroundColor: '#4a6cf7'
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

new Chart(document.getElementById('graficoTipo'), {
    type: 'doughnut',
    data: {
        labels: ['Particular', 'Convênio'],
        datasets: [{
            data: [<?= $particular ?>, <?= $convenio ?>],
            backgroundColor: ['#4a6cf7', '#2ecc71']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>

</body>
</html>
