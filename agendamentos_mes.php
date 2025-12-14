<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$inicio = date("Y-m-01");
$fim    = date("Y-m-t");

$stmt = $conn->prepare("
    SELECT a.*, s.nome AS servico
    FROM agendamentos a
    JOIN servicos s ON s.id = a.servico_id
    WHERE a.data BETWEEN :inicio AND :fim
    ORDER BY a.data, a.hora
");
$stmt->execute([
    ':inicio' => $inicio,
    ':fim' => $fim
]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Agendamentos do Mês</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="main-content">
<h2>Agendamentos do Mês</h2>

<?php if (!$agendamentos): ?>
<p>Nenhum agendamento neste mês.</p>
<?php else: ?>
<table class="tabela">
<tr>
<th>Data</th>
<th>Paciente</th>
<th>Serviço</th>
<th>Hora</th>
</tr>
<?php foreach ($agendamentos as $a): ?>
<tr>
<td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
<td><?= htmlspecialchars($a['paciente']) ?></td>
<td><?= htmlspecialchars($a['servico']) ?></td>
<td><?= substr($a['hora'], 0, 5) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

</div>
</body>
</html>
