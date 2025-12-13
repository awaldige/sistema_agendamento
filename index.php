<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nomeUsuario = $_SESSION['user_nome'] ?? 'Administrador';

$hoje = date("Y-m-d");

$totalHoje = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = :hoje");
$totalHoje->execute([':hoje' => $hoje]);
$totalHoje = $totalHoje->fetchColumn();

$semanaInicio = date("Y-m-d", strtotime("monday this week"));
$semanaFim = date("Y-m-d", strtotime("sunday this week"));

$totalSemana = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN :i AND :f");
$totalSemana->execute([':i'=>$semanaInicio, ':f'=>$semanaFim]);
$totalSemana = $totalSemana->fetchColumn();

$mesInicio = date("Y-m-01");
$mesFim = date("Y-m-t");

$totalMes = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE data BETWEEN :i AND :f");
$totalMes->execute([':i'=>$mesInicio, ':f'=>$mesFim]);
$totalMes = $totalMes->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo</title>

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
        <li><a href="index.php"><i class="fas fa-chart-line"></i><span>Dashboard</span></a></li>
        <li><a href="agendamentos.php"><i class="fas fa-calendar-check"></i><span>Agendamentos</span></a></li>
        <li><a href="servicos.php"><i class="fas fa-briefcase"></i><span>Serviços</span></a></li>
        <li><a href="usuarios.php"><i class="fas fa-users"></i><span>Usuários</span></a></li>
    </ul>

    <div class="logout-box">
        <a class="logout-btn" href="logout.php">
            <i class="fas fa-sign-out-alt"></i><span>Sair</span>
        </a>
    </div>
</aside>

<!-- CONTEÚDO -->
<main class="main-content">
    <header class="desktop-header">
        <h1>Bem-vindo ao Painel</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <?= htmlspecialchars($nomeUsuario) ?>
        </div>
    </header>

    <section class="content-box">
        <h2>Visão Geral</h2>

        <div class="cards-dashboard">
            <a class="card-item">
                <i class="fas fa-clock"></i>
                <h3>Hoje</h3>
                <p><?= $totalHoje ?></p>
            </a>

            <a class="card-item">
                <i class="fas fa-calendar-week"></i>
                <h3>Semana</h3>
                <p><?= $totalSemana ?></p>
            </a>

            <a class="card-item">
                <i class="fas fa-calendar-alt"></i>
                <h3>Mês</h3>
                <p><?= $totalMes ?></p>
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
