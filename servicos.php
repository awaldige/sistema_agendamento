<?php
// servicos.php
require_once 'auth.php';
require_once 'conexao.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criar novo serviço
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? null;

    if ($nome === '') {
        $_SESSION['flash'] = 'Nome do serviço é obrigatório.';
        header('Location: servicos.php');
        exit;
    }

    $sql = "INSERT INTO servicos (nome, descricao, preco) VALUES (:nome, :descricao, :preco)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':nome'      => $nome,
        ':descricao' => $descricao ?: null,
        ':preco'     => $preco ?: null
    ]);

    $_SESSION['flash'] = 'Serviço criado com sucesso!';
    header('Location: servicos.php');
    exit;
}

// Listar serviços
$stmt = $conn->query("SELECT id, nome, descricao, preco FROM servicos ORDER BY nome ASC");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Serviços</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    margin: 0;
    padding: 0;
    background: #f4f6fb;
    font-family: "Poppins", sans-serif;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2c3e50;
}

/* Flash */
.flash {
    background: #dff3d6;
    padding: 12px;
    border-left: 5px solid #2ecc71;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
}

/* Form */
form {
    margin-bottom: 35px;
}

.form-group {
    margin-bottom: 18px;
}

input, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #d0d7e2;
    border-radius: 10px;
    background: #f9fbff;
    font-size: 15px;
    transition: .25s;
}

input:focus, textarea:focus {
    border-color: #4a6cf7;
    box-shadow: 0 0 0 3px rgba(74,108,247,0.15);
}

button {
    padding: 12px 20px;
    background: #4a6cf7;
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: .25s;
    font-size: 15px;
}

button:hover {
    background: #2649f5;
    box-shadow: 0 6px 14px rgba(74,108,247,0.25);
}

/* Tabela */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 14px;
    border-bottom: 1px solid #e1e6f0;
    text-align: left;
}

th {
    background: #f0f3fa;
    font-weight: 600;
}

.btn {
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    font-size: 13px;
}

.btn-edit {
    background: #4a6cf7;
}

.btn-edit:hover {
    background: #2649f5;
}

.btn-del {
    background: #e74c3c;
}

.btn-del:hover {
    background: #c0392b;
}

/* Voltar */
.voltar {
    display: inline-block;
    margin-bottom: 15px;
    padding: 10px 15px;
    background: #7f8c8d;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
}

.voltar:hover {
    background: #636e72;
}
</style>
</head>
<body>

<div class="container">

    <a href="index.php" class="voltar">← Voltar</a>

    <h2>Serviços</h2>

    <?php if ($flash): ?>
        <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <h3>Novo Serviço</h3>

    <form action="servicos.php" method="POST">

        <div class="form-group">
            <input type="text" name="nome" required placeholder="Nome do serviço">
        </div>

        <div class="form-group">
            <textarea name="descricao" rows="3" placeholder="Descrição (opcional)"></textarea>
        </div>

        <div class="form-group">
            <input type="number" name="preco" step="0.01" placeholder="Preço (opcional)">
        </div>

        <button type="submit">Criar Serviço</button>
    </form>

    <h3>Lista de Serviços</h3>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($servicos as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['nome']) ?></td>
                <td>
                    <?= $s['preco'] !== null ? "R$ " . number_format($s['preco'], 2, ',', '.') : '-' ?>
                </td>
                <td>
                    <a class="btn btn-edit" href="servico_edit.php?id=<?= $s['id'] ?>">Editar</a>
                    <a class="btn btn-del" href="servico_delete.php?id=<?= $s['id'] ?>" onclick="return confirm('Excluir serviço?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
