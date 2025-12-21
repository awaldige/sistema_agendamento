<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* BUSCA SERVIÇOS */
$stmt = $conn->query("SELECT id, nome FROM servicos ORDER BY nome");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* SALVAR AGENDAMENTO */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "INSERT INTO agendamentos
        (paciente, email, telefone, data, hora, servico_id, tipo_consulta, observacoes)
        VALUES
        (:paciente, :email, :telefone, :data, :hora, :servico, :tipo, :obs)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':paciente' => $_POST['paciente'],
        ':email'    => $_POST['email'] ?? null,
        ':telefone' => $_POST['telefone'] ?? null,
        ':data'     => $_POST['data'],
        ':hora'     => $_POST['hora'],
        ':servico'  => $_POST['servico_id'],
        ':tipo'     => $_POST['tipo_consulta'],
        ':obs'      => $_POST['observacoes'] ?? null
    ]);

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Agendamento</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    background: #eef2f7;
    font-family: "Poppins", sans-serif;
}
.container {
    max-width: 700px;
    margin: 60px auto;
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
}
.voltar {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    background: #7f8c8d;
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}
h2 {
    margin-bottom: 20px;
}
form {
    display: grid;
    gap: 15px;
}
input, select, textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
textarea {
    resize: vertical;
}
button {
    background: #4a6cf7;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
}
</style>
</head>

<body>

<div class="container">

<a href="index.php" class="voltar">← Voltar</a>

<h2>Novo Agendamento</h2>

<form method="POST">

    <input type="text" name="paciente" placeholder="Nome do paciente" required>

    <input type="email" name="email" placeholder="E-mail">

    <input type="text" name="telefone" placeholder="Telefone">

    <input type="date" name="data" required>

    <input type="time" name="hora" required>

    <select name="servico_id" required>
        <option value="">Selecione o serviço</option>
        <?php foreach ($servicos as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="tipo_consulta" required>
        <option value="">Tipo de consulta</option>
        <option value="particular">Particular</option>
        <option value="convenio">Convênio</option>
    </select>

    <textarea name="observacoes" placeholder="Observações"></textarea>

    <button type="submit">Salvar Agendamento</button>

</form>

</div>

</body>
</html>
