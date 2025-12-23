<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* Campos realmente obrigatórios */
$obrigatorios = [
    'paciente',
    'data',
    'hora',
    'servico_id',
    'tipo_consulta'
];

foreach ($obrigatorios as $campo) {
    if (empty($_POST[$campo])) {
        die("Todos os campos obrigatórios devem ser preenchidos.");
    }
}

$sql = "INSERT INTO agendamentos
(paciente, email, telefone, data, hora, servico_id, tipo_consulta, observacoes)
VALUES
(:paciente, :email, :telefone, :data, :hora, :servico_id, :tipo_consulta, :observacoes)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':paciente'      => $_POST['paciente'],
    ':email'         => $_POST['email'] ?? null,
    ':telefone'      => $_POST['telefone'] ?? null,
    ':data'          => $_POST['data'],
    ':hora'          => $_POST['hora'],
    ':servico_id'    => $_POST['servico_id'],
    ':tipo_consulta' => $_POST['tipo_consulta'],
    ':observacoes'   => $_POST['observacoes'] ?? null
]);

header("Location: agendamentos.php");
exit();
