<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Receber dados do formulário
$paciente       = trim($_POST['paciente'] ?? '');
$email          = trim($_POST['email'] ?? '');
$telefone       = trim($_POST['telefone'] ?? '');
$servico_id     = $_POST['servico'] ?? '';   // <-- CORRIGIDO AQUI
$tipo_consulta  = $_POST['tipo_consulta'] ?? '';
$data           = $_POST['data'] ?? '';
$hora           = $_POST['hora'] ?? '';
$observacoes    = trim($_POST['observacoes'] ?? '');

// Validação
if (
    empty($paciente) ||
    empty($email) ||
    empty($telefone) ||
    empty($servico_id) ||
    empty($tipo_consulta) ||
    empty($data) ||
    empty($hora)
) {
    die("Todos os campos são obrigatórios.");
}

// Inserir
$sql = "INSERT INTO agendamentos 
        (paciente, email, telefone, servico_id, tipo_consulta, data, hora, observacoes)
        VALUES 
        (:paciente, :email, :telefone, :servico_id, :tipo_consulta, :data, :hora, :observacoes)";

$stmt = $conn->prepare($sql);

$ok = $stmt->execute([
    ':paciente'      => $paciente,
    ':email'         => $email,
    ':telefone'      => $telefone,
    ':servico_id'    => $servico_id,
    ':tipo_consulta' => $tipo_consulta,
    ':data'          => $data,
    ':hora'          => $hora,
    ':observacoes'   => $observacoes
]);

if ($ok) {
    header("Location: agendamentos.php?sucesso=1");
    exit();
} else {
    echo "Erro ao salvar.";
}
?>
