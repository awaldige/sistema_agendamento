<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM servicos WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: servicos.php");
exit();
