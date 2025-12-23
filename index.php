<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* ===== ANO SELECIONADO ===== */
$anoSelecionado = $_GET['ano'] ?? date('Y');

/* ===== GRÁFICO POR MÊS ===== */
$stmt = $conn->prepare("
    SELECT EXTRACT(MONTH FROM data) AS mes, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(YEAR FROM data) = :ano
    GROUP BY mes
");
$stmt->execute([':ano' => $anoSelecionado]);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* MESES FIXOS */
$meses = [
    1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
    5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
    9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
];

$totaisMes = array_fill(1, 12, 0);

foreach ($resultado as $r) {
    $totaisMes[(int)$r['mes']] = (int)$r['total'];
}
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
<aside class="sidebar" id="sidebar">
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
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </li>
    </ul>
</aside>

<!-- MAIN -->
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
    <h2>Gráficos de Consultas</h2>

    <!-- SELETOR DE ANO -->
    <form method="GET" style="margin:15px 0">
        <label><strong>Ano:</strong></label>
        <select name="ano" onchange="this.form.submit()">
            <?php
            for ($y = date('Y'); $y >= date('Y') - 5; $y--) {
                $selected = ($y == $anoSelecionado) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>
    </form>

    <!-- GRÁFICOS -->
    <div class="dashboard-graficos">
        <div class="grafico-box">
            <h3>Consultas por Mês</h3>
            <canvas id="graficoMes"></canvas>
        </div>
    </div>
</section>

</main>

<script>
function toggleMenu() {
    document.getElementById('sidebar').classList.toggle('open');
}

/* GRÁFICO */
new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_values($meses)) ?>,
        datasets: [{
            label: 'Consultas em <?= $anoSelecionado ?>',
            data: <?= json_encode(array_values($totaisMes)) ?>,
            backgroundColor: '#4a6cf7'
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
