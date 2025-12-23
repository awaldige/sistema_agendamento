<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$filtro = $_GET['filtro'] ?? null;
$mesSelecionado = $_GET['mes'] ?? date('m');
$anoSelecionado = $_GET['ano'] ?? date('Y');

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

/* MÊS (com seletor) */
else {
    $where = "WHERE EXTRACT(MONTH FROM data) = :mes
              AND EXTRACT(YEAR FROM data) = :ano";

    $params = [
        ':mes' => (int)$mesSelecionado,
        ':ano' => (int)$anoSelecionado
    ];
}

$sql = "SELECT * FROM agendamentos
        $where
        ORDER BY data ASC, hora ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Agendamentos</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    background: #eef2f7;
    font-family: "Poppins", sans-serif;
}
.container {
    max-width: 1100px;
    margin: 60px auto;
    background: #fff;
    padding: 40px;
    border-radius: 16px;
}
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.voltar {
    text-decoration: none;
    background: #7f8c8d;
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}
.novo {
    text-decoration: none;
    background: #4a6cf7;
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}
.filtro {
    margin: 20px 0;
    display: flex;
    gap: 10px;
}
select, button {
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}
th {
    background: #f4f6f9;
}
</style>
</head>

<body>

<div class="container">

<div class="top-bar">
    <a href="index.php" class="voltar">← Voltar ao Menu</a>

    <!-- ✅ BOTÃO NOVO AGENDAMENTO APENAS SEM FILTRO -->
    <?php if (!$filtro): ?>
        <a href="novo_agendamento.php" class="novo">+ Novo Agendamento</a>
    <?php endif; ?>
</div>

<h2>Agendamentos</h2>

<!-- 🔍 FILTRO POR MÊS E ANO -->
<form method="GET" class="filtro">
    <select name="mes">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m == $mesSelecionado ? 'selected' : '' ?>>
                <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="ano">
        <?php for ($a = date('Y') - 3; $a <= date('Y') + 1; $a++): ?>
            <option value="<?= $a ?>" <?= $a == $anoSelecionado ? 'selected' : '' ?>>
                <?= $a ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

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

<?php if ($agendamentos): ?>
    <?php foreach ($agendamentos as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['paciente']) ?></td>
        <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
        <td><?= substr($a['hora'], 0, 5) ?></td>
        <td><?= ucfirst(htmlspecialchars($a['tipo_consulta'])) ?></td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="4">Nenhum agendamento encontrado.</td>
</tr>
<?php endif; ?>

</tbody>
</table>

</div>

</body>
</html>
