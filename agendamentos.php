<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$filtro = $_GET['filtro'] ?? 'mes';
$mesSelecionado = $_GET['mes'] ?? date('Y-m');

$where = "";
$params = [];

/* HOJE */
if ($filtro === 'hoje') {
    $where = "WHERE data = CURRENT_DATE";
}

/* SEMANA */
elseif ($filtro === 'semana') {
    $where = "WHERE data BETWEEN
        (CURRENT_DATE - INTERVAL '1 day' * EXTRACT(DOW FROM CURRENT_DATE))
        AND
        (CURRENT_DATE + INTERVAL '1 day' * (6 - EXTRACT(DOW FROM CURRENT_DATE)))";
}

/* MÊS */
else {
    [$ano, $mes] = explode('-', $mesSelecionado);

    $where = "WHERE EXTRACT(MONTH FROM data) = :mes
              AND EXTRACT(YEAR FROM data) = :ano";

    $params = [
        ':mes' => (int)$mes,
        ':ano' => (int)$ano
    ];
}

$sql = "SELECT * FROM agendamentos $where ORDER BY data ASC, hora ASC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Agendamentos</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== FILTROS ===== */
.filtros {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.filtros a, .filtros button {
    padding: 10px 14px;
    background: #4a6cf7;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.filtros a.secondary {
    background: #7f8c8d;
}

/* ===== TABELA ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

th {
    background: #f4f6f9;
    text-align: left;
}

/* ===== MOBILE CARDS ===== */
.cards {
    display: none;
}

.card {
    background: #fff;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

.card strong {
    display: block;
    font-size: 16px;
}

.card span {
    font-size: 14px;
    color: #555;
}

/* ===== RESPONSIVO ===== */
@media (max-width: 768px) {
    table {
        display: none;
    }

    .cards {
        display: block;
    }
}
</style>
</head>

<body>

<main class="main-content">

<header>
    <h2>Agendamentos</h2>
</header>

<div class="filtros">
    <a href="agendamentos.php?filtro=hoje">Hoje</a>
    <a href="agendamentos.php?filtro=semana">Semana</a>

    <form method="GET">
        <input type="month" name="mes" value="<?= $mesSelecionado ?>" onchange="this.form.submit()">
        <input type="hidden" name="filtro" value="mes">
    </form>

    <a href="novo_agendamento.php" class="secondary">
        <i class="fas fa-plus"></i> Novo
    </a>

    <a href="index.php" class="secondary">
        <i class="fas fa-arrow-left"></i> Menu
    </a>
</div>

<!-- DESKTOP -->
<table>
<thead>
<tr>
    <th>Paciente</th>
    <th>Data</th>
    <th>Hora</th>
    <th>Tipo</th>
</tr>
</thead>
<tbody>
<?php if ($agendamentos): foreach ($agendamentos as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['paciente']) ?></td>
    <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
    <td><?= substr($a['hora'], 0, 5) ?></td>
    <td><?= ucfirst($a['tipo_consulta']) ?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="4">Nenhum agendamento.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- MOBILE -->
<div class="cards">
<?php foreach ($agendamentos as $a): ?>
    <div class="card">
        <strong><?= htmlspecialchars($a['paciente']) ?></strong>
        <span>📅 <?= date('d/m/Y', strtotime($a['data'])) ?></span><br>
        <span>⏰ <?= substr($a['hora'], 0, 5) ?></span><br>
        <span>📌 <?= ucfirst($a['tipo_consulta']) ?></span>
    </div>
<?php endforeach; ?>
</div>

</main>

</body>
</html>
