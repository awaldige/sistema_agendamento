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

/* ===== MÊS ANTERIOR ===== */
$mesAnterior = $mes - 1;
$anoAnterior = $ano;

if ($mesAnterior === 0) {
    $mesAnterior = 12;
    $anoAnterior--;
}

/* ===== ANOS ===== */
$anosDB = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data)::int
    FROM agendamentos
")->fetchAll(PDO::FETCH_COLUMN);

$anoAtual = (int)date('Y');
$anos = array_unique(array_merge(range($anoAtual - 5, $anoAtual), $anosDB));
rsort($anos);

/* ===== TOTAL MÊS ATUAL ===== */
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
");
$stmt->execute([':mes'=>$mes, ':ano'=>$ano]);
$totalMes = (int)$stmt->fetchColumn();

/* ===== TOTAL MÊS ANTERIOR ===== */
$stmt->execute([':mes'=>$mesAnterior, ':ano'=>$anoAnterior]);
$totalMesAnterior = (int)$stmt->fetchColumn();

/* ===== VARIAÇÃO ===== */
if ($totalMesAnterior > 0) {
    $variacao = (($totalMes - $totalMesAnterior) / $totalMesAnterior) * 100;
} else {
    $variacao = $totalMes > 0 ? 100 : 0;
}
$variacao = round($variacao, 1);

/* ===== CONSULTAS POR DIA ===== */
$stmt = $conn->prepare("
    SELECT EXTRACT(DAY FROM data)::int AS dia, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    GROUP BY dia
");
$stmt->execute([':mes'=>$mes, ':ano'=>$ano]);
$consultasPorDia = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

/* ===== TIPO DE CONSULTA ===== */
$stmt = $conn->prepare("
    SELECT tipo_consulta, COUNT(*) AS total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    GROUP BY tipo_consulta
");
$stmt->execute([':mes'=>$mes, ':ano'=>$ano]);
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

<style>
.cards {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:25px;
}
.card {
    background:#fff;
    padding:22px;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}
.card h3 {font-size:14px;color:#7f8c8d;}
.card .valor {font-size:32px;font-weight:700;margin-top:8px;}
.card .variacao.up {color:#27ae60;}
.card .variacao.down {color:#e74c3c;}
.card .variacao.neutral {color:#7f8c8d;}
</style>
</head>

<body>

<aside class="sidebar">
    <ul class="menu">
        <li><a class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
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

<form method="GET" style="display:flex;gap:10px;margin-bottom:20px;">
<select name="mes">
<?php foreach ($meses as $n=>$m): ?>
<option value="<?= $n ?>" <?= $n==$mes?'selected':'' ?>><?= $m ?></option>
<?php endforeach; ?>
</select>
<select name="ano">
<?php foreach ($anos as $a): ?>
<option value="<?= $a ?>" <?= $a==$ano?'selected':'' ?>><?= $a ?></option>
<?php endforeach; ?>
</select>
<button>Atualizar</button>
</form>

<!-- 📊 CARDS -->
<div class="cards">
    <div class="card">
        <h3>Total de Consultas</h3>
        <div class="valor" id="totalMes">0</div>
        <div><?= $meses[$mes] ?>/<?= $ano ?></div>
    </div>

    <div class="card">
        <h3>Comparação com mês anterior</h3>
        <div class="valor"><?= $totalMesAnterior ?></div>
        <div><?= $meses[$mesAnterior] ?>/<?= $anoAnterior ?></div>
    </div>

    <div class="card">
        <h3>Variação</h3>
        <div class="valor variacao <?= $variacao>0?'up':($variacao<0?'down':'neutral') ?>">
            <?= $variacao ?>%
        </div>
        <div>em relação ao mês anterior</div>
    </div>
</div>

<div class="dashboard-graficos">
    <div class="grafico-box">
        <h4>Consultas por dia</h4>
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
function toggleMenu(){
    document.querySelector('.sidebar').classList.toggle('open');
}

/* CONTADOR ANIMADO */
let total = <?= $totalMes ?>;
let contador = 0;
const el = document.getElementById('totalMes');
const intervalo = setInterval(()=>{
    contador++;
    el.innerText = contador;
    if(contador >= total) clearInterval(intervalo);
}, 20);

/* GRÁFICOS */
const dadosPHP = <?= json_encode($consultasPorDia) ?>;
const dias = Array.from({length:31},(_,i)=>i+1);
const dadosDias = dias.map(d=>dadosPHP[d]??0);

new Chart(graficoMes,{
    type:'bar',
    data:{labels:dias,datasets:[{data:dadosDias,backgroundColor:'#4a6cf7'}]},
    options:{responsive:true,scales:{y:{beginAtZero:true}}}
});

new Chart(graficoTipo,{
    type:'doughnut',
    data:{
        labels:<?= json_encode(array_map(fn($d)=>ucfirst($d['tipo_consulta']),$dadosTipo)) ?>,
        datasets:[{data:<?= json_encode(array_map(fn($d)=>(int)$d['total'],$dadosTipo)) ?>}]
    },
    options:{responsive:true,cutout:'65%'}
});
</script>

</body>
</html>
