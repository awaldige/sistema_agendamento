<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* ===== FILTROS ===== */
$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

/* ===== ANOS DISPONÍVEIS NO BANCO ===== */
$anos = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data) AS ano
    FROM agendamentos
    ORDER BY ano DESC
")->fetchAll(PDO::FETCH_COLUMN);

/* ===== CONSULTAS POR MÊS / ANO ===== */
$stmt = $conn->prepare("
    SELECT EXTRACT(MONTH FROM data) AS mes, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    GROUP BY mes
");
$stmt->execute([
    ':mes' => (int)$mes,
    ':ano' => (int)$ano
]);
$dadosMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===== TIPO DE CONSULTA ===== */
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

    <!-- 🔎 FILTRO MÊS / ANO -->
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <select name="mes">
            <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>" <?= $m==$mes?'selected':'' ?>>
                    <?= strftime('%B', mktime(0,0,0,$m,1)) ?>
                </option>
            <?php endfor; ?>
        </select>

        <select name="ano">
            <?php foreach ($anos as $a): ?>
                <option value="<?= $a ?>" <?= $a==$ano?'selected':'' ?>>
                    <?= $a ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Atualizar</button>

        <a href="index.php" class="secondary" style="padding:10px 14px;border-radius:8px;background:#7f8c8d;color:#fff;text-decoration:none;">
            ← Menu
        </a>
    </form>

    <!-- 📊 GRÁFICOS -->
    <div class="dashboard-graficos">

        <div class="grafico-box">
            <h4>Consultas – <?= strftime('%B', mktime(0,0,0,$mes,1)) ?>/<?= $ano ?></h4>
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

/* GRÁFICO MÊS */
new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: ['Consultas'],
        datasets: [{
            label: 'Total',
            data: [<?= $dadosMes[0]['total'] ?? 0 ?>],
            backgroundColor: '#4a6cf7'
        }]
    }
});

/* GRÁFICO TIPO */
new Chart(document.getElementById('graficoTipo'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(fn($d)=>ucfirst($d['tipo_consulta']), $dadosTipo)) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn($d)=>$d['total'], $dadosTipo)) ?>,
            backgroundColor: ['#4a6cf7','#2ecc71','#f39c12']
        }]
    }
});
</script>

</body>
</html>
