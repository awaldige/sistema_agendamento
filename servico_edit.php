<?php
require_once 'auth.php';
require_once 'conexao.php';

// Verifica ID
if (!isset($_GET['id'])) {
    header("Location: servicos.php");
    exit;
}

$id = intval($_GET['id']);

// Buscar serviço
$stmt = $conn->prepare("SELECT * FROM servicos WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$servico = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servico) {
    die("Serviço não encontrado.");
}

// Atualizar serviço
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? null;

    if ($nome === '') {
        $erro = "O nome do serviço é obrigatório.";
    } else {

        $sql = "UPDATE servicos SET nome = :nome, descricao = :descricao, preco = :preco WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome,
            ':descricao' => $descricao ?: null,
            ':preco'     => $preco ?: null,
            ':id'        => $id
        ]);

        header("Location: servicos.php?atualizado=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Serviço</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    margin: 0;
    padding: 0;
    background: #f4f6fb;
    font-family: "Poppins", sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    width: 100%;
    max-width: 520px;
    background: #fff;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    animation: fadeIn .4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
    color: #2c3e50;
}

/* Formularios */
.form-group {
    margin-bottom: 18px;
    position: relative;
}

input, textarea {
    width: 100%;
    padding: 14px;
    border: 1px solid #d0d7e2;
    border-radius: 10px;
    outline: none;
    font-size: 15px;
    background: #f9fbff;
    transition: .25s;
}

input:focus, textarea:focus {
    border-color: #4a6cf7;
    box-shadow: 0 0 0 3px rgba(74,108,247,0.15);
}

/* Botão */
button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: #4a6cf7;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: .25s;
    margin-top: 10px;
}

button:hover {
    background: #2649f5;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(74,108,247,0.25);
}

/* Voltar */
.voltar {
    display: inline-block;
    text-decoration: none;
    color: #fff;
    background: #7f8c8d;
    padding: 10px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.voltar:hover {
    background: #636e72;
}

/* Erro */
.error {
    background: #ffd6d6;
    border-left: 5px solid #e74c3c;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>
</head>

<body>

<div class="container">

    <a href="servicos.php" class="voltar">← Voltar</a>

    <h2>Editar Serviço</h2>

    <?php if (!empty($erro)): ?>
        <div class="error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="form-group">
            <input type="text" name="nome" value="<?= htmlspecialchars($servico['nome']) ?>" required>
        </div>

        <div class="form-group">
            <textarea name="descricao" rows="3"><?= htmlspecialchars($servico['descricao']) ?></textarea>
        </div>

        <div class="form-group">
            <input type="number" step="0.01" name="preco" 
                   value="<?= $servico['preco'] !== null ? htmlspecialchars($servico['preco']) : '' ?>"
                   placeholder="Preço (opcional)">
        </div>

        <button type="submit">Salvar Alterações</button>
    </form>
</div>

</body>
</html>
