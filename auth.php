<?php
// auth.php
session_start();
if (!isset($_SESSION['user_id'])) {
    // redireciona para login se não autenticado
    header('Location: login.php');
    exit;
}
?>
