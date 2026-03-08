<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: servicos.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM servicos WHERE id = :id");
$stmt->execute([':id' => $id]);
$servico = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servico) {
    header("Location: servicos.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    $sql = "UPDATE servicos SET nome=:n, descricao=:d WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':n' => $nome,
        ':d' => $descricao,
        ':id' => $id
    ]);

    header("Location: servicos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviço</title>

    <link rel="icon" href="agendamento_medico.jpg" type="image/jpeg">

    <style>
        body {
            background-color: #f3f6fb;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width:500px;
            margin:60px auto;
            background:#fff;
            padding:30px;
            border-radius:16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { color: #333; margin-top: 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #666; }
        input, textarea {
            width:100%;
            padding:12px;
            margin-bottom:15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* Garante que o padding não estoure a largura */
        }
        .btn { 
            background:#4a6cf7; 
            color:#fff; 
            padding:12px 20px; 
            border-radius:8px; 
            border: none; 
            cursor: pointer; 
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn:hover { background: #3b5be0; }
        .menu { background:#7f8c8d; margin-left: 10px; }
        .menu:hover { background: #6c7a7b; }
    </style>
</head>
<body>

<div class="container">
    <h2>Editar Serviço</h2>

    <form method="post">
        <label>Nome do Serviço</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($servico['nome']) ?>" required>
        
        <label>Descrição</label>
        <textarea name="descricao" rows="4"><?= htmlspecialchars($servico['descricao']) ?></textarea>

        <div style="margin-top: 10px;">
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="servicos.php" class="btn menu">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>
