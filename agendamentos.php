<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$filtro = $_GET['filtro'] ?? null;
$where = "";
$params = [];
$titulo = "Agendamentos";

if ($filtro === 'hoje') {
    $titulo = "Agendamentos de Hoje";
    $where = "WHERE data = ?";
    $params[] = date("Y-m-d");

} elseif ($filtro === 'semana') {
    $titulo = "Agendamentos da Semana";
    $where = "WHERE data BETWEEN ? AND ?";
    $params[] = date("Y-m-d", strtotime("monday this week"));
    $params[] = date("Y-m-d", strtotime("sunday this week"));

} elseif ($filtro === 'mes') {
    $titulo = "Agendamentos do Mês";
    $where = "WHERE data BETWEEN ? AND ?";
    $params[] = date("Y-m-01");
    $params[] = date("Y-m-t");
}

$sql = "
SELECT a.*, s.nome AS servico
FROM agendamentos a
LEFT JOIN servicos s ON s.id = a.servico_id
$where
ORDER BY data, hora
";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= $titulo ?></title>
<style>
body { font-family: Poppins, sans-serif; background:#f4f6fb; }
.container { max-width:1000px; margin:40px auto; background:#fff; padding:30px; border-radius:14px; }
.topo { display:flex; justify-content:space-between; align-items:center; }
a.btn { padding:10px 14px; border-radius:8px; text-decoration:none; color:#fff; }
.novo { background:#4a6cf7; }
.voltar { background:#7f8c8d; }
table { width:100%; margin-top:20px; border-collapse:collapse; }
th, td { padding:12px; border-bottom:1px solid #eee; }
th { background:#f0f2f7; }
</style>
</head>
<body>

<div class="container">

<div class="topo">
    <h2><?= $titulo ?></h2>

    <?php if (!$filtro): ?>
        <a href="novo_agendamento.php" class="btn novo">+ Novo Agendamento</a>
    <?php else: ?>
        <a href="index.php" class="btn voltar">← Voltar</a>
    <?php endif; ?>
</div>

<table>
<tr>
    <th>Paciente</th>
    <th>Serviço</th>
    <th>Data</th>
    <th>Hora</th>
    <th>Tipo</th>
</tr>

<?php if (!$dados): ?>
<tr><td colspan="5">Nenhum agendamento encontrado</td></tr>
<?php endif; ?>

<?php foreach ($dados as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['paciente']) ?></td>
    <td><?= htmlspecialchars($a['servico']) ?></td>
    <td><?= date("d/m/Y", strtotime($a['data'])) ?></td>
    <td><?= $a['hora'] ?></td>
    <td><?= ucfirst($a['tipo_consulta']) ?></td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
