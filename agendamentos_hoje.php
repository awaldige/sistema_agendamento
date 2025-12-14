<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$hoje = date("Y-m-d");

$stmt = $conn->prepare("
    SELECT a.*, s.nome AS servico
    FROM agendamentos a
    JOIN servicos s ON s.id = a.servico_id
    WHERE a.data = :hoje
    ORDER BY a.hora
");
$stmt->execute([':hoje' => $hoje]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos de Hoje</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="main-content">
    <h2>Agendamentos de Hoje</h2>

    <?php if (!$agendamentos): ?>
        <p>Nenhum agendamento para hoje.</p>
    <?php else: ?>
        <table class="tabela">
            <tr>
                <th>Paciente</th>
                <th>Serviço</th>
                <th>Hora</th>
            </tr>
            <?php foreach ($agendamentos as $a): ?>
                <tr>
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
