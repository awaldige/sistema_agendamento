<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$filtro = $_GET['filtro'] ?? 'mes';
$mesSelecionado = $_GET['mes'] ?? date('Y-m');

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
else {
    [$ano, $mes] = explode('-', $mesSelecionado);

    $where = "WHERE EXTRACT(MONTH FROM data) = :mes
              AND EXTRACT(YEAR FROM data) = :ano";

    $params = [
        ':mes' => (int)$mes,
        ':ano' => (int)$ano
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
    max-width: 1000px;
    margin: 60px auto;
    background: #fff;
    padding: 40px;
    border-radius: 16px;
}

/* TOPO */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-header h2 {
    margin: 0;
}

/* BOTÕES */
.btn {
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-voltar {
    background: #7f8c8d;
    color: #fff;
}

.btn-novo {
    background: #4a6cf7;
    color: #fff;
}

.btn:hover {
    opacity: 0.9;
}

/* TABELA */
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
    text-align: left;
}
</style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <h2>Agendamentos</h2>

        <!-- 🔹 BOTÃO QUE ABRE O FORMULÁRIO -->
        <a href="novo_agendamento.php" class="btn btn-novo">
            ➕ Novo Agendamento
        </a>
    </div>

    <!-- 🔹 VOLTAR SEMPRE PARA O MENU -->
    <a href="index.php" class="btn btn-voltar">← Voltar ao menu</a>

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
