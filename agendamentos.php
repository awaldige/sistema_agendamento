<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* CONTROLE DE FILTRO */
$filtro = $_GET['filtro'] ?? null;
$mostrarLista = false;
$agendamentos = [];

if ($filtro) {
    $mostrarLista = true;

    switch ($filtro) {

        case 'hoje':
            $sql = "SELECT * FROM agendamentos WHERE data = CURDATE() ORDER BY hora";
            break;

        case 'semana':
            $sql = "SELECT * FROM agendamentos
                    WHERE data BETWEEN
                    DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                    AND DATE_ADD(CURDATE(), INTERVAL (6 - WEEKDAY(CURDATE())) DAY)
                    ORDER BY data, hora";
            break;

        case 'mes':
            $sql = "SELECT * FROM agendamentos
                    WHERE MONTH(data) = MONTH(CURDATE())
                    AND YEAR(data) = YEAR(CURDATE())
                    ORDER BY data, hora";
            break;

        default:
            $sql = "SELECT * FROM agendamentos ORDER BY data, hora";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
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
    font-family: Poppins, sans-serif;
}
.container {
    max-width: 900px;
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

<h2>Agendamentos</h2>

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
                <td><?= htmlspecialchars($a['paciente'] ?? '') ?></td>
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

    <a href="agendamentos.php" class="btn-novo">+ Novo Agendamento</a>

<?php else: ?>

    <a href="novo_agendamento.php" class="btn-novo">+ Novo Agendamento</a>

<?php endif; ?>

</div>

</body>
</html>
