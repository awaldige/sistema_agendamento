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
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    margin: 0;
    background: #eef2f7;
    font-family: 'Poppins', sans-serif;
}

.dashboard {
    max-width: 1200px;
    margin: 60px auto;
    padding: 0 20px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
}

.dashboard-header h1 {
    font-size: 28px;
    color: #2c3e50;
}

.user-info {
    font-size: 14px;
    color: #555;
}

.dashboard-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.overview-card {
    background: #fff;
    border-radius: 20px;
    padding: 26px;
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none;
    color: #2c3e50;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    transition: .3s ease;
}

.overview-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,.15);
}

.icon {
    width: 62px;
    height: 62px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: #fff;
}

.today .icon {
    background: linear-gradient(135deg, #f39c12, #f1c40f);
}

.week .icon {
    background: linear-gradient(135deg, #4a6cf7, #6a8bff);
}

.month .icon {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
}

.info span {
    font-size: 14px;
    color: #7f8c8d;
}

.info strong {
    display: block;
    font-size: 34px;
    margin: 4px 0;
}

.info small {
    font-size: 13px;
    color: #95a5a6;
}

/* Responsivo */
@media (max-width: 600px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>
</head>

<body>

<div class="dashboard">

    <header class="dashboard-header">
        <h1>Visão Geral</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <?= htmlspecialchars($nomeUsuario) ?>
        </div>
    </header>

    <section class="dashboard-overview">

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

    </section>

</div>

</body>
</html>
