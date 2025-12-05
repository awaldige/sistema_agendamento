<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id           = $_POST['id'] ?? '';
$paciente     = trim($_POST['paciente'] ?? '');
$email        = trim($_POST['email'] ?? '');
$telefone     = trim($_POST['telefone'] ?? '');
$servico_id   = $_POST['servico_id'] ?? '';
$tipo_consulta = $_POST['tipo_consulta'] ?? '';
$data         = $_POST['data'] ?? '';
$hora         = $_POST['hora'] ?? '';
$observacoes  = trim($_POST['observacoes'] ?? '');

if (empty($id) || empty($paciente) || empty($email) || empty($telefone) ||
    empty($servico_id) || empty($tipo_consulta) || empty($data) || empty($hora)) {
    die("Todos os campos obrigatórios devem ser preenchidos.");
}

$sql = "UPDATE agendamentos SET
        paciente = :paciente,
        email = :email,
        telefone = :telefone,
        servico_id = :servico_id,
        tipo_consulta = :tipo_consulta,
        data = :data,
        hora = :hora,
        observacoes = :observacoes
        WHERE id = :id";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ':paciente' => $paciente,
    ':email' => $email,
    ':telefone' => $telefone,
    ':servico_id' => $servico_id,
    ':tipo_consulta' => $tipo_consulta,
    ':data' => $data,
    ':hora' => $hora,
    ':observacoes' => $observacoes,
    ':id' => $id
]);

header("Location: agendamentos.php?editado=1");
exit();
