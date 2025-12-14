<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$hoje = date('Y-m-d');
$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE data = :hoje");
$stmt->execute([':hoje' => $hoje]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Agendamentos de Hoje</h2>
<pre><?php print_r($agendamentos); ?></pre>
