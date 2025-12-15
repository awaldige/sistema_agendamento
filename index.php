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
   TOTAIS DE AGENDAMENTOS
============================= */

$hoje = date("Y-m-d");

/* Hoje */
$stmtHoje = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = :hoje");
$stmtHoje->execute([':hoje' => $hoje]);
$totalHoje = $stmtHoje->fetchColumn();

/* Semana */
$semanaInicio = date("Y-m-d", strtotime("monday this week"));
$semanaFim    = date("Y-m-d", strtotime("sunday this week"));

$stmtSemana = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN :i AND :f");
$stmtSemana->execute([
    ':i' => $semanaInicio,
    ':f' => $semanaFim
]);
$totalSemana = $stmtSemana->fetchColumn();

/* Mês */
$mesInicio = date("Y-m-01");
$mesFim    = date("Y-m-t");

$stmtMes = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN :i AND :f");
$stmtMes->execute([
    ':i' => $mesInicio,
    ':f' => $mesFim
]);
$totalMes = $stmtMes->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel Administrativo</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- HEADER MOBILE -->
<header class="topbar-mobile">
    <button id="toggleBtn"><i class="fas fa-bars"></i></button>
    <span>Admin</span>
</header>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>Admin</h2>
    </div>

    <ul class="menu">
        <li>
            <a href="index.php" class="active">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="agendamentos.php">
                <i class="fas fa-calendar-check"></i>
                <span>Agendamentos</span>
            </a>
        </li>
        <li>
            <a href="servicos.php">
                <i class="fas fa-briefcase"></i>
                <span>Serviços</span>
            </a>
        </li>
        <li>
            <a href="usuarios.php">
                <i class="fas fa-users"></i>
                <span>Usuários</span>
            </a>
        </li>
    </ul>

    <div class="logout-box">
        <a class="logout-btn" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>

<!-- CONTEÚDO -->
<main class="main-content">

    <!-- HEADER DESKTOP -->
    <header class="desktop-header">
        <h1>Painel Administrativo</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <?= htmlspecialchars($nomeUsuario) ?>
        </div>
    </header>

    <!-- VISÃO GERAL -->
    <section class="content-box">
        <h2>Visão Geral</h2>

        <div class="dashboard-overview">

            <a href="agendamentos.php?filtro=hoje" class="overview-card today">
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info">
                    <span>Hoje</span>
                    <strong><?= $totalHoje ?></strong>
                    <small>Agendamentos</small>
                </div>
            </a>

            <a href="agendamentos.php?filtro=semana" class="overview-card week">
                <div class="icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="info">
                    <span>Semana</span>
                    <strong><?= $totalSemana ?></strong>
                    <small>Agendamentos</small>
                </div>
            </a>

            <a href="agendamentos.php?filtro=mes" class="overview-card month">
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info">
                    <span>Mês</span>
                    <strong><?= $totalMes ?></strong>
                    <small>Agendamentos</small>
                </div>
            </a>

        </div>
    </section>

</main>

<script>
document.getElementById('toggleBtn').onclick = () => {
    document.getElementById('sidebar').classList.toggle('open');
};
</script>

</body>
</html>
