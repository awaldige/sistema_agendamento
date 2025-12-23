<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: agendamentos.php");
    exit();
}

/* Buscar agendamento */
$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id = ?");
$stmt->execute([$id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header("Location: agendamentos.php");
    exit();
}

/* Serviços */
$servicos = $conn->query("SELECT id, nome FROM servicos ORDER BY nome")->fetchAll();

/* Salvar edição */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE agendamentos SET
        paciente = ?, email = ?, telefone = ?, data = ?, hora = ?,
        servico_id = ?, tipo_consulta = ?, observacoes = ?
        WHERE id = ?";

    $conn->prepare($sql)->execute([
        $_POST['paciente'],
        $_POST['email'],
        $_POST['telefone'],
        $_POST['data'],
        $_POST['hora'],
        $_POST['servico_id'],
        $_POST['tipo_consulta'],
        $_POST['observacoes'],
        $id
    ]);

    header("Location: agendamentos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Agendamento</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<a href="agendamentos.php" class="voltar">← Voltar</a>

<h2>Editar Agendamento</h2>

<form method="POST">
<input type="text" name="paciente" value="<?= $agendamento['paciente'] ?>" required>
<input type="email" name="email" value="<?= $agendamento['email'] ?>">
<input type="text" name="telefone" value="<?= $agendamento['telefone'] ?>">
<input type="date" name="data" value="<?= $agendamento['data'] ?>" required>
<input type="time" name="hora" value="<?= substr($agendamento['hora'],0,5) ?>" required>

<select name="servico_id">
<?php foreach ($servicos as $s): ?>
<option value="<?= $s['id'] ?>" <?= $s['id']==$agendamento['servico_id']?'selected':'' ?>>
<?= $s['nome'] ?>
</option>
<?php endforeach; ?>
</select>

<select name="tipo_consulta">
<option value="particular" <?= $agendamento['tipo_consulta']=='particular'?'selected':'' ?>>Particular</option>
<option value="convenio" <?= $agendamento['tipo_consulta']=='convenio'?'selected':'' ?>>Convênio</option>
</select>

<textarea name="observacoes"><?= $agendamento['observacoes'] ?></textarea>

<button type="submit">Salvar Alterações</button>
</form>
</div>

</body>
</html>
