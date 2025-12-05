<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    die("ID inválido.");
}

$sql = "DELETE FROM agendamentos WHERE id = :id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);

header("Location: agendamentos.php?excluido=1");
exit();
