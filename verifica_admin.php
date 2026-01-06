<?php
// verifica_admin.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verifica se existe uma sessão de usuário
// 2. Verifica se o nível do usuário é 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['nivel'] !== 'admin') {
    // Se não for admin, redireciona para o painel comum ou login
    header("Location: login.php?erro=restrito");
    exit;
}
