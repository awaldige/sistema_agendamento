<?php
/**
 * ESTE É APENAS UM EXEMPLO DO ARQUIVO DE CONEXÃO.
 * O arquivo REAL (conexao.php) não deve ir para o GitHub.
 */

$host = "localhost";
$usuario = "SEU_USUARIO_AQUI";
$senha = "SUA_SENHA_AQUI";
$banco = "NOME_DO_BANCO_AQUI";

try {
    // Exemplo de conexão (não funcional sem ajustes)
    $conn = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados.";
    exit;
}
