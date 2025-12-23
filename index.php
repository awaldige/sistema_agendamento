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

        <!-- ✅ AGENDAMENTOS ABRE O FORMULÁRIO -->
        <li>
            <a href="novo_agendamento.php">
                <i class="fas fa-calendar-check"></i> Agendamentos
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
    </ul>
</aside>

<main class="main-content">
    <h1>Bem-vindo, <?= htmlspecialchars($nomeUsuario) ?></h1>

    <div class="dashboard-overview">
        <a href="agendamentos.php?filtro=hoje" class="overview-card">
            <strong><?= $totalHoje ?></strong>
            <span>Hoje</span>
        </a>

        <a href="agendamentos.php?filtro=semana" class="overview-card">
            <strong><?= $totalSemana ?></strong>
            <span>Semana</span>
        </a>

        <a href="agendamentos.php?filtro=mes" class="overview-card">
            <strong><?= $totalMes ?></strong>
            <span>Mês</span>
        </a>
    </div>
</main>

</body>
</html>
