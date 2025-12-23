<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

$stmt = $conn->prepare("
    SELECT * FROM agendamentos
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
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.filtros select, .filtros a {
    padding: 10px;
    border-radius: 8px;
}
.cards { display:none; }

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
            <?php for($y=date('Y');$y>=date('Y')-5;$y--): ?>
                <option value="<?= $y ?>" <?= $y==$ano?'selected':'' ?>><?= $y ?></option>
            <?php endfor; ?>
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
<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* ==========================
   FILTRO SOMENTE POR MÊS/ANO
========================== */

$mesSelecionado = $_GET['mes'] ?? date('Y-m');
[$ano, $mes] = explode('-', $mesSelecionado);

$sql = "SELECT * FROM agendamentos
        WHERE EXTRACT(MONTH FROM data) = :mes
          AND EXTRACT(YEAR FROM data) = :ano
        ORDER BY data ASC, hora ASC";

$stmt = $conn->prepare($sql);
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
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Consultar Consultas</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== FILTROS ===== */
.filtros {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    align-items: center;
}

.filtros input[type="month"] {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.filtros a {
    padding: 10px 14px;
    background: #7f8c8d;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
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

td a {
    margin-right: 10px;
    text-decoration: none;
    font-size: 16px;
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

.card-actions {
    margin-top: 10px;
    display: flex;
    gap: 12px;
}

.card-actions a {
    text-decoration: none;
    font-size: 15px;
}

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
    <h2>Consultar Consultas</h2>
</header>

<!-- 🔎 FILTRO POR MÊS -->
<div class="filtros">
    <form method="GET">
        <input type="month" name="mes" value="<?= $mesSelecionado ?>" onchange="this.form.submit()">
    </form>

    <a href="index.php">
        <i class="fas fa-arrow-left"></i> Menu
    </a>
</div>

<!-- ===== DESKTOP ===== -->
<table>
<thead>
<tr>
    <th>Paciente</th>
    <th>Data</th>
    <th>Hora</th>
    <th>Tipo</th>
    <th>Ações</th>
</tr>
</thead>
<tbody>
<?php if ($agendamentos): foreach ($agendamentos as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['paciente']) ?></td>
    <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
    <td><?= substr($a['hora'], 0, 5) ?></td>
    <td><?= ucfirst($a['tipo_consulta']) ?></td>
    <td>
        <a href="editar_agendamento.php?id=<?= $a['id'] ?>">✏️</a>
        <a href="excluir_agendamento.php?id=<?= $a['id'] ?>"
           onclick="return confirm('Deseja excluir esta consulta?')">🗑</a>
    </td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="5">Nenhuma consulta encontrada.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- ===== MOBILE ===== -->
<div class="cards">
<?php if ($agendamentos): foreach ($agendamentos as $a): ?>
    <div class="card">
        <strong><?= htmlspecialchars($a['paciente']) ?></strong>
        <div>📅 <?= date('d/m/Y', strtotime($a['data'])) ?></div>
        <div>⏰ <?= substr($a['hora'], 0, 5) ?></div>
        <div>📌 <?= ucfirst($a['tipo_consulta']) ?></div>

        <div class="card-actions">
            <a href="editar_agendamento.php?id=<?= $a['id'] ?>">✏️ Editar</a>
            <a href="excluir_agendamento.php?id=<?= $a['id'] ?>"
               onclick="return confirm('Excluir consulta?')">🗑 Excluir</a>
        </div>
    </div>
<?php endforeach; else: ?>
    <p>Nenhuma consulta encontrada.</p>
<?php endif; ?>
</div>

</main>

</body>
</html>

