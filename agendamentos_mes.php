<?php
session_start();
require_once 'conexao.php';

$inicio = date("Y-m-01");
$fim    = date("Y-m-t");

$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE data BETWEEN :i AND :f");
$stmt->execute([':i'=>$inicio, ':f'=>$fim]);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Agendamentos do Mês</h2>
<pre><?php print_r($dados); ?></pre>
