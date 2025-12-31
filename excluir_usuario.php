<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: usuarios.php");
    exit();
}

// NÃO pode excluir o próprio usuário
if ($id === (int)$_SESSION['user_id']) {
    header("Location: usuarios.php?erro=proprio");
    exit();
}

// Busca usuário
$stmt = $conn->prepare("SELECT username FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}

// NÃO permite excluir o admin
if ($usuario['username'] === 'admin') {
    header("Location: usuarios.php?erro=admin");
    exit();
}

// Exclui
$del = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
$del->execute([':id' => $id]);

header("Location: usuarios.php?excluido=1");
exit();
