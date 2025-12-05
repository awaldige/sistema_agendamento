<?php
/**
 * ESTE É APENAS UM EXEMPLO DO ARQUIVO DE LOGIN.
 * Não contém lógica real nem acesso ao banco.
 * A versão REAL (login.php) não vai para o GitHub.
 */

session_start();

// Exemplo de configuração
// (na versão real, você faz consulta ao banco)
$usuario_demo = "admin";
$senha_demo   = "senha_criptografada";

// Processamento simplificado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($username === "" || $senha === "") {
        $erro = "Preencha o usuário e a senha.";
    } else {
        // Exemplo — sem validação real
        $erro = "Login real apenas na versão privada (login.php).";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Exemplo</title>
</head>

<body>
    <h2>Exemplo de Tela de Login</h2>
    <p>Este arquivo é apenas um modelo. O login real fica no login.php (não enviado ao GitHub).</p>
</body>

</html>
