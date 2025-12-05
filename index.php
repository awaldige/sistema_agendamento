<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

/* =============================
   BUSCAS PARA OS CARDS
============================= */

// Total de agendamentos
$totalAgendamentos = $conn->query("SELECT COUNT(*) FROM agendamentos")->fetchColumn();

// Total de serviços
$totalServicos = $conn->query("SELECT COUNT(*) FROM servicos")->fetchColumn();

// Total de usuários
$totalUsuarios = $conn->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

/* ---------- AGENDAMENTOS HOJE ---------- */
$hoje = date("Y-m-d");
$stmtHoje = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = :hoje");
$stmtHoje->execute([':hoje' => $hoje]);
$totalHoje = $stmtHoje->fetchColumn();

/* ---------- AGENDAMENTOS DA SEMANA (segunda a domingo) ---------- */
$semanaInicio = date("Y-m-d", strtotime("monday this week"));
$semanaFim     = date("Y-m-d", strtotime("sunday this week"));

$stmtSemana = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN :inicio AND :fim");
$stmtSemana->execute([':inicio' => $semanaInicio, ':fim' => $semanaFim]);

$totalSemana = $stmtSemana->fetchColumn();

/* ---------- AGENDAMENTOS DO MÊS ---------- */
$mesInicio = date("Y-m-01");
$mesFim    = date("Y-m-t");

$stmtMes = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN :inicio AND :fim");
$stmtMes->execute([':inicio' => $mesInicio, ':fim' => $mesFim]);

$totalMes = $stmtMes->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .cards-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

/* CARD */
.card-item {
    background: #ffffff;
    padding: 28px 22px;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: .25s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    border-left: 6px solid #4a6cf7;
}

.card-item i {
    font-size: 40px;
    margin-bottom: 12px;
    color: #4a6cf7;
}

.card-item h3 {
    font-size: 20px;
    margin-bottom: 8px;
    font-weight: 600;
}

.card-item p {
    font-size: 26px;
    font-weight: bold;
    color: #222;
    margin-top: 5px;
}

/* HOVER */
.card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    border-left-width: 8px;
}
    </style>
</head>

<body>

    <!-- MENU LATERAL -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Admin</h2>
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <ul class="menu">
            <li><a class="active" href="index.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li><a href="agendamentos.php"><i class="fas fa-calendar-check"></i> <span>Agendamentos</span></a></li>
            <li><a href="servicos.php"><i class="fas fa-briefcase"></i> <span>Serviços</span></a></li>
            <li><a href="usuarios.php"><i class="fas fa-users"></i> <span>Usuários</span></a></li>
        </ul>

        <div class="logout-box">
            <a class="logout-btn" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
        </div>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="main-content">
        <header>
            <h1>Bem-vindo ao Painel</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($nomeUsuario) ?></span>
            </div>
        </header>

        <section class="content-box">
            <h2>Visão Geral</h2>

            <div class="cards-dashboard">

                <a href="agendamentos_hoje.php" class="card-item">
                    <i class="fas fa-clock"></i>
                    <h3>Agendamentos de Hoje</h3>
                    <p><?= $totalHoje ?></p>
                </a>

                <a href="agendamentos_semana.php" class="card-item">
                    <i class="fas fa-calendar-week"></i>
                    <h3>Agendamentos da Semana</h3>
                    <p><?= $totalSemana ?></p>
                </a>                

                <a href="agendamentos_mes.php" class="card-item">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Agendamentos do Mês</h3>
                    <p><?= $totalMes ?></p>
                </a>        

            </div>

        </section>
    </div>

    <script src="script.js"></script>
</body>

</html>
