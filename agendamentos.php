<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* =============================
   FILTRO
============================= */

$filtro = $_GET['filtro'] ?? 'hoje'; // padrão: HOJE
$mesSelecionado = $_GET['mes'] ?? null;

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
elseif ($filtro === 'mes') {

    if (!empty($mesSelecionado)) {
        [$ano, $mes] = explode('-', $mesSelecionado);

        $where = "WHERE EXTRACT(MONTH FROM data) = :mes
                  AND EXTRACT(YEAR FROM data) = :ano";

        $params = [
            ':mes' => (int)$mes,
            ':ano' => (int)$ano
        ];
    } else {
        $where = "WHERE EXTRACT(MONTH FROM data) = EXTRACT(MONTH FROM CURRENT_DATE)
                  AND EXTRACT(YEAR FROM data) = EXTRACT(YEAR FROM CURRENT_DATE)";
    }
}

/* BUSCA AGENDAMENTOS */
$sql = "SELECT *
        FROM agendamentos
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
    float: right;
    background: #4a6cf7;
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
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

<a href="index.php" class="voltar">← Voltar</a>
<a href="novo_agendamento.php" class="btn-novo">+ Novo Agendamento</a>

<h2>Agendamentos</h2>

<?php if ($filtro === 'mes'): ?>
<form method="GET" style="margin:20px 0;">
    <input type="hidden" name="filtro" value="mes">
    <input type="month" name="mes" value="<?= htmlspecialchars($mesSelecionado ?? date('Y-m')) ?>">
    <button type="submit">Buscar</button>
</form>
<?php endif; ?>

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

</div>

</body>
</html>
