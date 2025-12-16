<?php

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("DATABASE_URL não configurada no Render.");
}

$parts = parse_url($databaseUrl);

$host = $parts['host'];
$port = $parts['port'];
$user = $parts['user'];
$pass = $parts['pass'];
$db   = ltrim($parts['path'], '/');

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
