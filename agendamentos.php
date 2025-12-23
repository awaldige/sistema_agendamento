<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

/* 🔹 ANOS EXISTENTES NO BANCO */
$anos = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data) AS ano
    FROM agendamentos
    ORDER BY ano DESC
")->fetchAll(PDO::FETCH_COLUMN);

/* 🔹 CONSULTAS FILTRADAS */
$stmt = $conn->prepare("
    SELECT *
    FROM agendamentos
    WHERE EXTRACT(MONTH FROM data) = :mes
      AND EXTRACT(YEAR FROM data) = :ano
    ORDER BY data, hora
");
$stmt->execute([
    ':mes' => (int)$mes,
    ':ano' => (int)$ano
]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Consultar Consultas</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">

<style>
.filtros {
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:20px;
}
.filtros select, .filtros button, .filtros a {
    padding:10px 14px;
    border-radius:8px;
    border:none;
}
.filtros button {
    background:#4a6cf7;
    color:#fff;
}
.filtros a {
    background:#7f8c8d;
    color:#fff;
    text-decoration:none;
}

.cards { display:none; }
.card {
    background:#fff;
    padding:16px;
    border-radius:14px;
    margin-bottom:14px;
    box-shadow:0 6px 16px rgba(0,0,0,.08);
}

@media(max-width:768px){
    table { display:none; }
    .cards { display:block; }
}
</style>
</head>

<body>
<main class="main-content">

<header>
    <h2>Consultar Consultas</h2>
</header>

<div class="filtros">
<form method="GET">
    <select name="mes">
        <?php for($m=1;$m<=12;$m++): ?>
            <option value="<?= $m ?>" <?= $m==$mes?'selected':'' ?>>
                <?= strftime('%B', mktime(0,0,0,$m,1)) ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="ano">
        <?php foreach ($anos as $y): ?>
            <option value="<?= $y ?>" <?= $y==$ano?'selected':'' ?>>
                <?= $y ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

<a href="index.php">← Menu</a>
</div>

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
<?php if($agendamentos): foreach($agendamentos as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['paciente']) ?></td>
    <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
    <td><?= substr($a['hora'],0,5) ?></td>
    <td><?= ucfirst($a['tipo_consulta']) ?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="4">Nenhuma consulta encontrada.</td></tr>
<?php endif; ?>
</tbody>
</table>

<div class="cards">
<?php foreach($agendamentos as $a): ?>
<div class="card">
    <strong><?= htmlspecialchars($a['paciente']) ?></strong><br>
    📅 <?= date('d/m/Y', strtotime($a['data'])) ?><br>
    ⏰ <?= substr($a['hora'],0,5) ?><br>
    📌 <?= ucfirst($a['tipo_consulta']) ?>
</div>
<?php endforeach; ?>
</div>

</main>
</body>
</html>
