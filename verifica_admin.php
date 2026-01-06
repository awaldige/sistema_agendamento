<?php
// verifica_admin.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Criamos variáveis seguras verificando se as chaves existem
$usuarioLogado = isset($_SESSION['user_id']);
$nivelUsuario  = isset($_SESSION['nivel']) ? $_SESSION['nivel'] : '';

// 2. Verifica se NÃO é admin
if (!$usuarioLogado || $nivelUsuario !== 'admin') {
    
    // Se ainda não enviamos nada para o navegador, redireciona via PHP
    if (!headers_sent()) {
        header("Location: login.php?erro=restrito");
        exit;
    } else {
        // Fallback: Redirecionamento via JavaScript caso o PHP já tenha enviado texto
        echo "<script>window.location.href='login.php?erro=restrito';</script>";
        exit;
    }
}
