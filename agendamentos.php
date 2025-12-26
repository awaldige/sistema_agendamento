<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* MESES PT-BR */
$meses = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

$anos = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM data)::int AS ano
    FROM agendamentos
    ORDER BY ano DESC
")->fetchAll(PDO::FETCH_COLUMN);

$stmt = $conn->prepare("
    SELECT * FROM agendamentos
    WHERE EXTRACT(MONTH FROM data)=:mes
      AND EXTRACT(YEAR FROM data)=:ano
    ORDER BY data,hora
");
$stmt->execute([':mes'=>$mes, ':ano'=>$ano]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
.filtros{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}
.filtros select,.filtros button,.filtros a{
    padding:10px;border-radius:8px;border:none;font-size:14px
}
.filtros button{background:#4a6cf7;color:#fff}
.filtros a{background:#7f8c8d;color:#fff;text-decoration:none}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{padding:12px;border-bottom:1px solid #ddd}
th{background:#f4f6f9}
.actions a{margin-right:8px;text-decoration:none}
.cards{display:none}
.card{background:#fff;padding:16px;border-radius:14px;margin-bottom:14px}
@media(max-width:768px){
    table{display:none}
    .cards{display:block}
}
</style>
</head>

<body>
<main class="main-content">

<header><h2>Consultar Consultas</h2></header>

<div class="filtros">
<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">
    <select name="mes">
        <?php foreach($meses as $n=>$m): ?>
            <option value="<?= $n ?>" <?= $n==$mes?'selected':'' ?>><?= $m ?></option>
        <?php endforeach; ?>
    </select>

    <select name="ano">
        <?php foreach($anos as $a): ?>
            <option value="<?= $a ?>" <?= $a==$ano?'selected':'' ?>><?= $a ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit"><i class="fas fa-search"></i> Pesquisar</button>
</form>

<a href="index.php"><i class="fas fa-arrow-left"></i> Menu</a>
</div>

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
<?php foreach($agendamentos as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['paciente']) ?></td>
    <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
    <td><?= substr($a['hora'],0,5) ?></td>
    <td><?= ucfirst($a['tipo_consulta']) ?></td>
    <td class="actions">
        <a href="editar_agendamento.php?id=<?= $a['id'] ?>">✏️</a>
        <a href="excluir_agendamento.php?id=<?= $a['id'] ?>"
           onclick="return confirm('Deseja excluir esta consulta?')">🗑</a>
    </td>
</tr>
<?php endforeach; ?>

<?php if(!$agendamentos): ?>
<tr><td colspan="5">Nenhuma consulta encontrada.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- MOBILE -->
<div class="cards">
<?php foreach($agendamentos as $a): ?>
<div class="card">
<strong><?= htmlspecialchars($a['paciente']) ?></strong><br>
📅 <?= date('d/m/Y', strtotime($a['data'])) ?><br>
⏰ <?= substr($a['hora'],0,5) ?><br>
📌 <?= ucfirst($a['tipo_consulta']) ?><br><br>
<a href="editar_agendamento.php?id=<?= $a['id'] ?>">✏️ Editar</a> |
<a href="excluir_agendamento.php?id=<?= $a['id'] ?>"
onclick="return confirm('Excluir consulta?')">🗑 Excluir</a>
</div>
<?php endforeach; ?>
</div>

</main>
</body>
</html>
