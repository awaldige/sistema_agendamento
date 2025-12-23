<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* ===== FILTROS ===== */
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

/* ===== ANOS DISPONÍVEIS ===== */
$anos = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data)::int AS ano
    FROM agendamentos
    ORDER BY ano DESC
")->fetchAll(PDO::FETCH_COLUMN);

/* ===== CONSULTAS ===== */
$stmt = $conn->prepare("
    SELECT *
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    ORDER BY data ASC, hora ASC
");
$stmt->execute([
    ':mes' => $mes,
    ':ano' => $ano
]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===== FORMATADOR DE MÊS (SEM STRFTIME) ===== */
$fmt = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'America/Sao_Paulo',
    IntlDateFormatter::GREGORIAN,
    'MMMM'
);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Consultar Consultas</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

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

.filtros select, .filtros button, .filtros a {
    padding: 10px 14px;
    border-radius: 8px;
    border: none;
    font-size: 14px;
}

.filtros button {
    background: #4a6cf7;
    color: #fff;
    cursor: pointer;
}

.filtros a {
    background: #7f8c8d;
    color: #fff;
    text-decoration: none;
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
}

/* ===== MOBILE ===== */
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

@media (max-width: 768px) {
    table { display: none; }
    .cards { display: block; }
}
</style>
</head>

<body>

<main class="main-content">

<header>
    <h2>
        Consultas – <?= ucfirst($fmt->format(mktime(0,0,0,$mes,1))) ?>/<?= $ano ?>
    </h2>
</header>

<!-- 🔎 FILTROS -->
<div class="filtros">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">
        <select name="mes">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m === $mes ? 'selected' : '' ?>>
                    <?= ucfirst($fmt->format(mktime(0,0,0,$m,1))) ?>
                </option>
            <?php endfor; ?>
        </select>

        <select name="ano">
            <?php foreach ($anos as $a): ?>
                <option value="<?= $a ?>" <?= $a === $ano ? 'selected' : '' ?>>
                    <?= $a ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filtrar</button>
    </form>

    <a href="index.php">
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
<tr>
    <td colspan="4">Nenhuma consulta encontrada.</td>
</tr>
<?php endif; ?>
</tbody>
</table>

<!-- MOBILE -->
<div class="cards">
<?php foreach ($agendamentos as $a): ?>
    <div class="card">
        <strong><?= htmlspecialchars($a['paciente']) ?></strong><br>
        📅 <?= date('d/m/Y', strtotime($a['data'])) ?><br>
        ⏰ <?= substr($a['hora'], 0, 5) ?><br>
        📌 <?= ucfirst($a['tipo_consulta']) ?>
    </div>
<?php endforeach; ?>
</div>

</main>

</body>
</html>
