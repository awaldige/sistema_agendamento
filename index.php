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
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
    7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

/* ===== FILTROS ===== */
$mes = (int)($_GET['mes'] ?? date('m'));
$ano = (int)($_GET['ano'] ?? date('Y'));

/* ===== MÊS ANTERIOR ===== */
$mesAnterior = $mes - 1;
$anoAnterior = $ano;
if ($mesAnterior === 0) { $mesAnterior = 12; $anoAnterior--; }

/* ===== ANOS ===== */
$anosDB = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data)::int AS ano
    FROM agendamentos
    ORDER BY ano DESC
")->fetchAll(PDO::FETCH_COLUMN);

$anoAtual = date('Y');
$anos = array_unique(array_merge(range($anoAtual-5,$anoAtual),$anosDB));
rsort($anos);

/* ===== TOTAL MÊS ===== */
$stmt = $conn->prepare("
    SELECT COUNT(*) FROM agendamentos
    WHERE EXTRACT(MONTH FROM data)=:mes
      AND EXTRACT(YEAR FROM data)=:ano
");
$stmt->execute(['mes'=>$mes,'ano'=>$ano]);
$totalMes = (int)$stmt->fetchColumn();

/* ===== TOTAL MÊS ANTERIOR ===== */
$stmt->execute(['mes'=>$mesAnterior,'ano'=>$anoAnterior]);
$totalMesAnterior = (int)$stmt->fetchColumn();

/* ===== VARIAÇÃO ===== */
$variacao = $totalMesAnterior>0
    ? round((($totalMes-$totalMesAnterior)/$totalMesAnterior)*100,1)
    : ($totalMes>0?100:0);

/* ===== CONSULTAS POR DIA ===== */
$stmt = $conn->prepare("
    SELECT EXTRACT(DAY FROM data)::int dia, COUNT(*) total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data)=:mes
      AND EXTRACT(YEAR FROM data)=:ano
    GROUP BY dia ORDER BY dia
");
$stmt->execute(['mes'=>$mes,'ano'=>$ano]);
$consultasPorDia = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

/* ===== MÉDIA MÓVEL ===== */
$dias = range(1,31);
$dadosDias = [];
foreach($dias as $d){
    $dadosDias[] = $consultasPorDia[$d] ?? 0;
}
$mediaMovel = [];
$janela = 3;
for($i=0;$i<count($dadosDias);$i++){
    $inicio = max(0,$i-$janela+1);
    $slice = array_slice($dadosDias,$inicio,$janela);
    $mediaMovel[] = round(array_sum($slice)/count($slice),2);
}

/* ===== TIPO DE CONSULTA ===== */
$stmt = $conn->prepare("
    SELECT tipo_consulta, COUNT(*) total
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data)=:mes
      AND EXTRACT(YEAR FROM data)=:ano
    GROUP BY tipo_consulta
");
$stmt->execute(['mes'=>$mes,'ano'=>$ano]);
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
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;margin-bottom:25px;
}
.card{
    background:#fff;padding:20px;border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}
.card h3{font-size:14px;color:#7f8c8d;}
.card .valor{font-size:32px;font-weight:700;}
.variacao.up{color:#27ae60}
.variacao.down{color:#e74c3c}
.dashboard-graficos{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:20px;
}
@media(max-width:768px){
    .dashboard-graficos{grid-template-columns:1fr;}
    .cards{grid-template-columns:1fr;}
}
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

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
<select name="mes"><?php foreach($meses as $n=>$m): ?>
<option value="<?= $n ?>" <?= $n==$mes?'selected':'' ?>><?= $m ?></option>
<?php endforeach ?></select>

<select name="ano"><?php foreach($anos as $a): ?>
<option value="<?= $a ?>" <?= $a==$ano?'selected':'' ?>><?= $a ?></option>
<?php endforeach ?></select>
<button>Atualizar</button>
</form>

<div class="cards">
<div class="card">
<h3>Total de Consultas</h3>
<div class="valor" id="totalMes">0</div>
</div>

<div class="card">
<h3>Mês Anterior</h3>
<div class="valor"><?= $totalMesAnterior ?></div>
</div>

<div class="card">
<h3>Variação</h3>
<div class="valor variacao <?= $variacao>=0?'up':'down' ?>"><?= $variacao ?>%</div>
</div>
</div>

<div class="dashboard-graficos">
<div class="grafico-box">
<h4>Consultas por Dia (com tendência)</h4>
<canvas id="graficoDia"></canvas>
</div>

<div class="grafico-box">
<h4>Tipo de Consulta</h4>
<canvas id="graficoTipo"></canvas>
</div>
</div>
</section>
</main>

<script>
function toggleMenu(){document.querySelector('.sidebar').classList.toggle('open');}

/* CONTADOR */
let total=<?= $totalMes ?>,c=0,el=document.getElementById('totalMes');
const i=setInterval(()=>{el.innerText=++c;if(c>=total)clearInterval(i)},20);

/* GRÁFICO LINHA + MÉDIA */
new Chart(document.getElementById('graficoDia'),{
type:'line',
data:{
labels:<?= json_encode($dias) ?>,
datasets:[
{
label:'Consultas',
data:<?= json_encode($dadosDias) ?>,
borderColor:'#4a6cf7',
backgroundColor:'rgba(74,108,247,.15)',
tension:.3,
fill:true
},
{
label:'Média móvel',
data:<?= json_encode($mediaMovel) ?>,
borderColor:'#e67e22',
borderDash:[6,6],
tension:.3
}
]},
options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});

/* TIPO */
new Chart(document.getElementById('graficoTipo'),{
type:'doughnut',
data:{
labels:<?= json_encode(array_column($dadosTipo,'tipo_consulta')) ?>,
datasets:[{data:<?= json_encode(array_column($dadosTipo,'total')) ?>,
backgroundColor:['#4a6cf7','#2ecc71','#f39c12','#9b59b6']}]
},
options:{responsive:true,cutout:'65%'}
});
</script>

</body>
</html>

