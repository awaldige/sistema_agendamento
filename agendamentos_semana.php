<?php
session_start();
require_once 'conexao.php';

$inicio = date("Y-m-d", strtotime("monday this week"));
$fim    = date("Y-m-d", strtotime("sunday this week"));

$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE data BETWEEN :i AND :f");
$stmt->execute([':i'=>$inicio, ':f'=>$fim]);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Agendamentos da Semana</h2>
<pre><?php print_r($dados); ?></pre>
