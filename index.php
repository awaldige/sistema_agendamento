<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

$hoje = date("Y-m-d");

/* HOJE */
$stmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ?");
$stmt->execute([$hoje]);
$totalHoje = $stmt->fetchColumn();

/* SEMANA */
$inicioSemana = date("Y-m-d", strtotime("monday this week"));
$fimSemana    = date("Y-m-d", strtotime("sunday this week"));
$stmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN ? AND ?");
$stmt->execute([$inicioSemana, $fimSemana]);
$totalSemana = $stmt->fetchColumn();

/* MÊS */
$inicioMes = date("Y-m-01");
$fimMes    = date("Y-m-t");
$stmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN ? AND ?");
$stmt->execute([$inicioMes, $fimMes]);
$totalMes = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<aside class="sidebar">
    <ul class="menu">
        <li>
            <a href="index.php" class="active">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="novo_agendamento.php">
                <i class="fas fa-calendar-plus"></i> Novo Agendamento
            </a>
        </li>

        <li>
            <a href="agendamentos.php">
                <i class="fas fa-calendar-check"></i> Consultar Agendamentos
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

<main class="main-content">

<header>
    <button class="toggle-btn" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="user-info">
        👋 <?= htmlspecialchars($nomeUsuario) ?>
    </div>
</header>

<div class="dashboard-overview">

    <a href="agendamentos.php?filtro=mes" class="overview-card month">
        <div class="icon"><i class="fas fa-chart-bar"></i></div>
        <div class="info">
            <span>Relatório</span>
            <strong>Mensal</strong>
            <small>Consultas</small>
        </div>
    </a>

    <a href="agendamentos.php?filtro=semana" class="overview-card week">
        <div class="icon"><i class="fas fa-calendar-week"></i></div>
        <div class="info">
            <span>Relatório</span>
            <strong>Semanal</strong>
            <small>Consultas</small>
        </div>
    </a>

    <a href="agendamentos.php" class="overview-card today">
        <div class="icon"><i class="fas fa-list"></i></div>
        <div class="info">
            <span>Consultar</span>
            <strong>Agenda</strong>
            <small>Completa</small>
        </div>
    </a>

</div>


</main>

<script>
function toggleMenu() {
    document.querySelector('.sidebar').classList.toggle('open');
}
</script>

</body>
</html>

