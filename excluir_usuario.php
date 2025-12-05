<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die('ID inválido.');
}

// Evitar auto-exclusão
if ($id == $_SESSION['user_id']) {
    die('Você não pode excluir seu próprio usuário enquanto estiver logado.');
}

// (Opcional) Evitar excluir último admin
// Conta admins
$stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE nivel = 'admin'");
$stmt->execute();
$totalAdmins = (int)$stmt->fetchColumn();
$stmt = $conn->prepare("SELECT nivel FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$nivelAlvo = $stmt->fetchColumn();

if ($nivelAlvo === 'admin' && $totalAdmins <= 1) {
    die('Não é possível excluir o último administrador.');
}

// Executa exclusão
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);

header("Location: usuarios.php?excluido=1");
exit();
