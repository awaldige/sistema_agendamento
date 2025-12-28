<?php
require 'conexao.php';

$stmt = $conn->query("SELECT NOW()");
echo "Conectado com sucesso: " . $stmt->fetchColumn();
