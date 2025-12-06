<?php
$host = getenv("DB_HOST");
$porta = getenv("DB_PORT");
$usuario = getenv("DB_USER");
$senha = getenv("DB_PASS");
$banco = getenv("DB_NAME");

try {
    $conn = new PDO("mysql:host=$host;port=$porta;dbname=$banco;charset=utf8", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}