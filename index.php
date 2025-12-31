<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* ===== MESES ===== */
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
    4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
    10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

/* ===== FILTROS ===== */
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

/* ===== ANOS DISPONÍVEIS (CORRETO) ===== */
$anosDB = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data)::int AS ano
    FROM agendamentos
    ORDER BY ano DESC
")->fetchAll(PDO::FETCH_COLUMN);

$anoAtual = (int)date('Y');
$anos = array_unique(array_merge(
    range($anoAtual - 5, $anoAtual),
    $anosDB
));
rsort($anos);

/* ===== CONSULTAS POR DIA ===== */
$stmt = $conn->prepare("
    SELECT EXTRACT(DAY FROM data)::int AS dia, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    GROUP BY dia
    ORDER BY dia
");
$stmt->execute([
    ':mes' => $mes,
    ':ano' => $ano
]);
$consultasPorDia = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

/* ===== TIPO DE CONSULTA ===== */
$stmt = $conn->prepare("
    SELECT tipo_consulta, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    GROUP BY tipo_consulta
");
$stmt->execute([
    ':mes' => $mes,
    ':ano' => $ano
]);
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

<aside class="sidebar">
    <ul class="menu">
        <li><a href="index.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="agendamentos.php"><i class="fas fa-calendar-check"></i> Consultas</a></li>
        <li><a href="novo_agendamento.php"><i class="fas fa-plus-circle"></i> Novo</a></li>
        <li><a href="servicos.php"><i class="fas fa-briefcase"></i> Serviços</a></li>
        <li><a href="usuarios.php"><i class="fas fa-users"></i> Usuários</a></li>
        <li><a href="logout.php" style="background:#c0392b;color:#fff;"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
    </ul>
</aside>

<main class="main-content">

<header>
    <button class="toggle-btn" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>
    <div class="user-info"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($nomeUsuario) ?></div>
</header>

<section class="content-box">
    <h2>Visão Geral</h2>

    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <select name="mes">
            <?php foreach ($meses as $num => $nome): ?>
                <option value="<?= $num ?>" <?= $num == $mes ? 'selected' : '' ?>><?= $nome ?></option>
            <?php endforeach; ?>
        </select>

        <select name="ano">
            <?php foreach ($anos as $a): ?>
                <option value="<?= $a ?>" <?= $a == $ano ? 'selected' : '' ?>><?= $a ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Atualizar</button>
    </form>

    <div class="dashboard-graficos">

        <div class="grafico-box">
            <h4>Consultas por dia — <?= $meses[$mes] ?>/<?= $ano ?></h4>
            <canvas id="graficoMes"></canvas>
        </div>

        <div class="grafico-box">
            <h4>Tipo de Consulta</h4>
            <canvas id="graficoTipo"></canvas>
        </div>

    </div>
</section>

</main>

<script>
function toggleMenu() {
    document.querySelector('.sidebar').classList.toggle('open');
}

/* ===== CONSULTAS POR DIA ===== */
const dadosPHP = <?= json_encode($consultasPorDia) ?>;
const dias = Array.from({length: 31}, (_, i) => i + 1);
const dadosDias = dias.map(d => dadosPHP[d] ?? 0);

new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: dias,
        datasets: [{
            label: 'Consultas',
            data: dadosDias,
            backgroundColor: '#4a6cf7'
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

/* ===== TIPO DE CONSULTA ===== */
new Chart(document.getElementById('graficoTipo'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(fn($d)=>ucfirst($d['tipo_consulta']), $dadosTipo)) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn($d)=>(int)$d['total'], $dadosTipo)) ?>,
            backgroundColor: ['#4a6cf7','#2ecc71','#f39c12','#9b59b6','#e74c3c']
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

</body>
</html>
