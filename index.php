<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* ===== DADOS PARA GRÁFICOS ===== */

// Consultas por mês (ano atual)
$anoAtual = date('Y');
$stmt = $conn->prepare("
    SELECT EXTRACT(MONTH FROM data) AS mes, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(YEAR FROM data) = :ano
    GROUP BY mes
    ORDER BY mes
");
$stmt->execute([':ano' => $anoAtual]);
$dadosMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tipos de consulta
$stmt = $conn->query("
    SELECT tipo_consulta, COUNT(*) AS total
    FROM agendamentos
    GROUP BY tipo_consulta
");
$dadosTipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<!-- SIDEBAR -->
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

        <li>
            <a href="logout.php" style="background:#c0392b;color:#fff;">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </li>
    </ul>
</aside>

<!-- CONTEÚDO -->
<main class="main-content">

<header>
    <button class="toggle-btn" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <?= htmlspecialchars($nomeUsuario) ?>
    </div>
</header>

<section class="content-box">
    <h2>Visão Geral</h2>

    <!-- 📊 GRÁFICOS -->
    <div class="dashboard-graficos">

        <div class="grafico-box">
            <h4>Consultas por Mês (<?= $anoAtual ?>)</h4>
            <canvas id="graficoMes"></canvas>
        </div>

        <div class="grafico-box">
            <h4>Tipo de Consulta</h4>
            <canvas id="graficoTipo"></canvas>
        </div>

    </div>
</section>

</main>

<!-- JS MENU -->
<script>
function toggleMenu() {
    document.querySelector('.sidebar').classList.toggle('open');
}
</script>

<!-- JS GRÁFICOS -->
<script>
const meses = <?= json_encode(array_map(fn($d) => 'Mês '.$d['mes'], $dadosMes)) ?>;
const totaisMes = <?= json_encode(array_map(fn($d) => $d['total'], $dadosMes)) ?>;

new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [{
            label: 'Consultas',
            data: totaisMes,
            backgroundColor: '#4a6cf7'
        }]
    }
});

const tipos = <?= json_encode(array_map(fn($d) => ucfirst($d['tipo_consulta']), $dadosTipo)) ?>;
const totaisTipo = <?= json_encode(array_map(fn($d) => $d['total'], $dadosTipo)) ?>;

new Chart(document.getElementById('graficoTipo'), {
    type: 'doughnut',
    data: {
        labels: tipos,
        datasets: [{
            data: totaisTipo,
            backgroundColor: ['#4a6cf7', '#2ecc71', '#f39c12']
        }]
    }
});
</script>

</body>
</html>
