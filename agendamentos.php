<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* =============================
   CONTROLE DE FILTROS
============================= */

$filtro = $_GET['filtro'] ?? null;
$mesSelecionado = $_GET['mes'] ?? null;

$mostrarLista = false;
$mostrarFiltroMes = false;

$agendamentos = [];
$where = "";
$params = [];

/* HOJE */
if ($filtro === 'hoje') {
    $mostrarLista = true;
    $where = "WHERE data = CURDATE()";
}

/* SEMANA */
elseif ($filtro === 'semana') {
    $mostrarLista = true;
    $where = "WHERE data BETWEEN
              DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
              AND DATE_ADD(CURDATE(), INTERVAL (6 - WEEKDAY(CURDATE())) DAY)";
}

/* MÊS */
elseif ($filtro === 'mes') {
    $mostrarLista = true;
    $mostrarFiltroMes = true;

    /* Mês selecionado OU mês atual */
    if (!empty($mesSelecionado)) {
        [$ano, $mes] = explode('-', $mesSelecionado);
        $where = "WHERE MONTH(data) = :mes AND YEAR(data) = :ano";
        $params = [
            ':mes' => $mes,
            ':ano' => $ano
        ];
    } else {
        $where = "WHERE MONTH(data) = MONTH(CURDATE())
                  AND YEAR(data) = YEAR(CURDATE())";
    }
}

/* EXECUTA QUERY */
if ($mostrarLista) {
    $sql = "SELECT * FROM agendamentos $where ORDER BY data ASC, hora ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
    max-width: 1000px;
    margin: 60px auto;
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
}
.voltar {
    text-decoration: none;
    background: #7f8c8d;
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}
.btn-novo {
    display: inline-block;
    margin: 20px 0;
    padding: 12px 20px;
    background: #4a6cf7;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
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

/* FILTRO MÊS */
.filtro-mes {
    display: flex;
    gap: 10px;
    align-items: center;
    margin: 20px 0;
    flex-wrap: wrap;
}
.filtro-mes label {
    font-weight: 500;
}
.filtro-mes input {
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.filtro-mes button {
    background: #4a6cf7;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
}
.filtro-mes a {
    background: #7f8c8d;
    color: #fff;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="container">

<a href="index.php" class="voltar">← Voltar</a>

<h2>Agendamentos</h2>

<!-- 🔎 FILTRO POR MÊS (SOMENTE NO CONTEXTO MÊS) -->
<?php if ($mostrarFiltroMes): ?>
<form method="GET" class="filtro-mes">
    <input type="hidden" name="filtro" value="mes">

    <label>Mês:</label>
    <input
        type="month"
        name="mes"
        value="<?= htmlspecialchars($mesSelecionado ?? date('Y-m')) ?>">

    <button type="submit">Buscar</button>

    <a href="agendamentos.php?filtro=mes">Mês Atual</a>
</form>
<?php endif; ?>

<a href="novo_agendamento.php" class="btn-novo">+ Novo Agendamento</a>

<?php if ($mostrarLista): ?>

    <?php if (count($agendamentos) > 0): ?>

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
        <?php foreach ($agendamentos as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['paciente']) ?></td>
                <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
                <td><?= substr($a['hora'], 0, 5) ?></td>
                <td><?= ucfirst(htmlspecialchars($a['tipo_consulta'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>
        <p>Nenhum agendamento encontrado.</p>
    <?php endif; ?>

<?php endif; ?>

</div>

</body>
</html>
