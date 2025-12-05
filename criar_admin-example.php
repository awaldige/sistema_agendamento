<?php
/**
 * ESTE É APENAS UM EXEMPLO DO ARQUIVO DE CRIAÇÃO DO USUÁRIO ADMIN.
 * O arquivo REAL (criar_admin.php) NÃO deve ir para o GitHub.
 *
 * Aqui mostramos apenas a estrutura, sem informações sensíveis.
 */

require_once 'conexao-example.php'; // exemplo de conexão

$nome     = "Administrador";
$username = "admin";
$senha    = "SENHA_AQUI"; // Na versão real, use password_hash()
$nivel    = "admin";

// Exemplo da estrutura de INSERT (não funcional)
$sql = "INSERT INTO usuarios (nome, username, senha, nivel) 
        VALUES (:nome, :username, :senha, :nivel)";

echo "Este é um exemplo. O arquivo real cria o usuário admin com segurança.";
