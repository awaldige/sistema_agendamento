<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se veio via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: agendamentos.php");
    exit();
}

// Campos do formulário
$id            = $_POST['id'];
$paciente      = $_POST['paciente'];
$email         = $_POST['email'];
$telefone      = $_POST['telefone'];
$data          = $_POST['data'];
$hora          = $_POST['hora'];
$servico_id    = $_POST['servico_id'];
$tipo_consulta = $_POST['tipo_consulta'];

// Atualizar agendamento
$sql = "UPDATE agendamentos 
        SET paciente = ?, email = ?, telefone = ?, data = ?, hora = ?, servico_id = ?, tipo_consulta = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$ok = $stmt->execute([
    $paciente,
    $email,
    $telefone,
    $data,
    $hora,
    $servico_id,
    $tipo_consulta,
    $id
]);

if ($ok) {
    header("Location: agendamentos.php?sucesso=1");
    exit();
} else {
    header("Location: agendamentos.php?erro=1");
    exit();
}
?>
