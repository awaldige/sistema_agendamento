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
<html>
<head>
<title>Editar Serviço</title>
<style>
.container {
    max-width:500px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:16px;
}
input, textarea {
    width:100%;
    padding:12px;
    margin-bottom:15px;
}
.btn { background:#4a6cf7; color:#fff; padding:10px 15px; border-radius:8px; }
.menu { background:#7f8c8d; }
</style>
</head>
<body>

<div class="container">
<h2>Editar Serviço</h2>

<form method="post">
<input type="text" name="nome" value="<?= htmlspecialchars($servico['nome']) ?>" required>
<textarea name="descricao"><?= htmlspecialchars($servico['descricao']) ?></textarea>

<button class="btn">Salvar</button>
<a href="servicos.php" class="btn menu">Cancelar</a>
</form>
</div>

</body>
</html>
