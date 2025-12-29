<?php
$host = 'aws-0-us-west-2.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';

// ⚠️ USUÁRIO TEM O ID DO PROJETO
$user = 'postgres.blpwyipmbhrbtxvqxhwr';
$password = 'UazCwgQaq0th6gE9';

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}
