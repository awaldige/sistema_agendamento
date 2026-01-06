<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pegamos o nome e o nível da sessão para controle de acesso
$nomeUsuario = $_SESSION['user_nome'] ?? 'Usuário';
$nivelUsuario = $_SESSION['nivel'] ?? 'colaborador';

/* ===== MESES ===== */
$meses = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
    7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

/* ===== FILTROS ===== */
$mes = (int)($_GET['mes'] ?? date('m'));
$ano = (int)($_GET['ano'] ?? date('Y'));

$mesAnterior = $mes - 1;
$anoAnterior = $ano;
if ($mesAnterior === 0) { $mesAnterior = 12; $anoAnterior--; }

/* ===== ANOS ===== */
$anosDB = $conn->query("SELECT DISTINCT EXTRACT(YEAR FROM data)::int AS ano FROM agendamentos ORDER BY ano DESC")->fetchAll(PDO::FETCH_COLUMN);
$anoAtual = date('Y');
$anos = array_unique(array_merge(range($anoAtual-5,$anoAtual),$anosDB));
rsort($anos);

/* ===== TOTAIS ===== */
$stmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE EXTRACT(MONTH FROM data)=:mes AND EXTRACT(YEAR FROM data)=:ano");
$stmt->execute(['mes'=>$mes,'ano'=>$ano]);
$totalMes = (int)$stmt->fetchColumn();

$stmt->execute(['mes'=>$mesAnterior,'ano'=>$anoAnterior]);
$totalMesAnterior = (int)$stmt->fetchColumn();
$variacao = $totalMesAnterior>0 ? round((($totalMes-$totalMesAnterior)/$totalMesAnterior)*100,1) : ($totalMes>0?100:0);

/* ===== DADOS GRÁFICO LINHA ===== */
$stmt = $conn->prepare("SELECT EXTRACT(DAY FROM data)::int dia, COUNT(*) total FROM agendamentos WHERE EXTRACT(MONTH FROM data)=:mes AND EXTRACT(YEAR FROM data)=:ano GROUP BY dia ORDER BY dia");
$stmt->execute(['mes'=>$mes,'ano'=>$ano]);
$consultasPorDia = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$dias = range(1,31);
$dadosDias = [];
foreach($dias as $d){ $dadosDias[] = $consultasPorDia[$d] ?? 0; }

$mediaMovel = []; $janela = 3;
for($i=0;$i<count($dadosDias);$i++){
    $inicio = max(0,$i-$janela+1);
    $slice = array_slice($dadosDias,$inicio,$janela);
    $mediaMovel[] = round(array_sum($slice)/count($slice),2);
}

/* ===== DADOS GRÁFICO TIPO ===== */
$stmt = $conn->prepare("SELECT tipo_consulta, COUNT(*) total FROM agendamentos WHERE EXTRACT(MONTH FROM data)=:mes AND EXTRACT(YEAR FROM data)=:ano GROUP BY tipo_consulta");
$stmt->execute(['mes'=>$mes,'ano'=>$ano]);
$dadosTipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard | Sistema Médico</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Estilos Locais para ajuste de tamanho */
    .cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:25px; }
    .card { background:#fff; padding:20px; border-radius:18px; box-shadow:0 10px 25px rgba(0,0,0,.08); }
    .card h3 { font-size:14px; color:#7f8c8d; margin-bottom:10px; }
    .card .valor { font-size:32px; font-weight:700; }
    .variacao.up { color:#27ae60 }
    .variacao.down { color:#e74c3c }

    /* CONTROLE DE TAMANHO DOS GRÁFICOS */
    .dashboard-graficos {
        display: grid;
        grid-template-columns: 1.4fr 1fr; /* Proporção equilibrada */
        gap: 20px;
        max-width: 1300px; /* Impede que os gráficos fiquem gigantes em monitores Ultra-Wide */
    }

    .grafico-box {
        background: #fff;
        padding: 20px;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,.08);
        display: flex;
        flex-direction: column;
    }

    .grafico-box h4 {
        margin-bottom: 15px;
        font-size: 15px;
        color: #34495e;
    }

    .chart-container {
        position: relative;
        height: 220px; /* ALTURA FIXA E ELEGANTE */
        width: 100%;
    }
    
    @media(max-width:1024px){
        .dashboard-graficos { grid-template-columns: 1fr; }
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
        
        <?php if ($nivelUsuario === 'admin'): ?>
            <li><a href="usuarios.php"><i class="fas fa-users"></i> Usuários</a></li>
        <?php endif; ?>
        
        <li><a href="logout.php" style="background:#c0392b;color:#fff; margin-top:30px;"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
    </ul>
</aside>

<main class="main-content">
    <header>
        <button class="toggle-btn" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>
        <div class="user-info">
            <i class="fas fa-user-circle"></i> 
            <?= htmlspecialchars($nomeUsuario) ?> 
            <span style="font-size:10px; background:#4a6cf7; color:white; padding:2px 8px; border-radius:12px; margin-left:8px; vertical-align:middle;">
                <?= strtoupper($nivelUsuario) ?>
            </span>
        </div>
    </header>

    <section class="content-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <h2 style="margin:0">Visão Geral</h2>
            <form method="GET" style="display:flex; gap:8px;">
                <select name="mes" style="padding:6px; border-radius:8px; border:1px solid #ddd;">
                    <?php foreach($meses as $n=>$m): ?>
                        <option value="<?= $n ?>" <?= $n==$mes?'selected':'' ?>><?= $m ?></option>
                    <?php endforeach ?>
                </select>
                <select name="ano" style="padding:6px; border-radius:8px; border:1px solid #ddd;">
                    <?php foreach($anos as $a): ?>
                        <option value="<?= $a ?>" <?= $a==$ano?'selected':'' ?>><?= $a ?></option>
                    <?php endforeach ?>
                </select>
                <button type="submit" style="background:#4a6cf7; color:white; border:none; padding:6px 15px; border-radius:8px; cursor:pointer;">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </form>
        </div>

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
                <div class="valor variacao <?= $variacao>=0?'up':'down' ?>">
                    <?= $variacao > 0 ? '+' : '' ?><?= $variacao ?>%
                </div>
            </div>
        </div>

        <div class="dashboard-graficos">
            <div class="grafico-box">
                <h4>Atendimentos por Dia</h4>
                <div class="chart-container">
                    <canvas id="graficoDia"></canvas>
                </div>
            </div>

            <div class="grafico-box">
                <h4>Distribuição por Tipo</h4>
                <div class="chart-container">
                    <canvas id="graficoTipo"></canvas>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function toggleMenu(){document.querySelector('.sidebar').classList.toggle('open');}

/* CONTADOR ANIMADO */
let total=<?= $totalMes ?>, c=0, el=document.getElementById('totalMes');
if(total > 0){
    const i=setInterval(()=>{
        c += Math.ceil(total/25);
        if(c >= total) { el.innerText = total; clearInterval(i); }
        else { el.innerText = c; }
    }, 30);
} else { el.innerText = "0"; }

/* GRÁFICO DE LINHA */
const ctxDia = document.getElementById('graficoDia');
if(ctxDia){
    new Chart(ctxDia, {
        type: 'line',
        data: {
            labels: <?= json_encode($dias) ?>,
            datasets: [{
                label: 'Consultas',
                data: <?= json_encode($dadosDias) ?>,
                borderColor: '#4a6cf7',
                backgroundColor: 'rgba(74, 108, 247, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 2
            }, {
                label: 'Média Móvel',
                data: <?= json_encode($mediaMovel) ?>,
                borderColor: '#e67e22',
                borderDash: [5, 5],
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // RESPEITA O CONTAINER
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
            scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
        }
    });
}

/* GRÁFICO DE PIZZA (DOUGHNUT) */
const ctxTipo = document.getElementById('graficoTipo');
if(ctxTipo){
    new Chart(ctxTipo, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($dadosTipo, 'tipo_consulta')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($dadosTipo, 'total')) ?>,
                backgroundColor: ['#4a6cf7', '#2ecc71', '#f39c12', '#9b59b6', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // RESPEITA O CONTAINER
            cutout: '70%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
        }
    });
}
</script>

</body>
</html>
