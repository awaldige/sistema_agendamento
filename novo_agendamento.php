<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servicos = $conn->query("SELECT id, nome FROM servicos ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Agendamento</title>
<style>
body { background:#f4f6fb; font-family:Poppins; }
.container { max-width:500px; margin:60px auto; background:#fff; padding:30px; border-radius:16px; }
input, select, textarea, button {
    width:100%; padding:12px; margin-bottom:12px;
    border-radius:10px; border:1px solid #ccc;
}
button { background:#4a6cf7; color:#fff; border:none; }
a { text-decoration:none; display:inline-block; margin-bottom:15px; }
</style>
</head>
<body>

<div class="container">
<a href="index.php">← Voltar ao Menu</a>

<h2>Novo Agendamento</h2>

<form action="salvar_agendamento.php" method="POST">
    <input type="text" name="paciente" placeholder="Paciente" required>
    <input type="email" name="email" placeholder="Email">
    <input type="text" name="telefone" placeholder="Telefone">
    <input type="date" name="data" required>
    <input type="time" name="hora" required>

    <select name="servico_id" required>
        <option value="">Selecione o serviço</option>
        <?php foreach ($servicos as $s): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="tipo_consulta" required>
        <option value="">Tipo de consulta</option>
        <option value="particular">Particular</option>
        <option value="convenio">Convênio</option>
    </select>

    <textarea name="observacoes" placeholder="Observações"></textarea>

    <button type="submit">Salvar</button>
</form>
</div>

</body>
</html>

