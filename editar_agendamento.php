<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id=:id");
$stmt->execute([':id'=>$id]);
$ag = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ag) die("Agendamento não encontrado");

if ($_POST) {
    $stmt = $conn->prepare("
        UPDATE agendamentos SET
        paciente=:paciente,data=:data,hora=:hora,tipo_consulta=:tipo
        WHERE id=:id
    ");
    $stmt->execute([
        ':paciente'=>$_POST['paciente'],
        ':data'=>$_POST['data'],
        ':hora'=>$_POST['hora'],
        ':tipo'=>$_POST['tipo_consulta'],
        ':id'=>$id
    ]);
    header("Location: agendamentos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Consulta</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<h2>Editar Consulta</h2>

<form method="POST">
<input type="text" name="paciente" value="<?= htmlspecialchars($ag['paciente']) ?>" required>
<input type="date" name="data" value="<?= $ag['data'] ?>" required>
<input type="time" name="hora" value="<?= substr($ag['hora'],0,5) ?>" required>

<select name="tipo_consulta">
    <option value="particular" <?= $ag['tipo_consulta']=='particular'?'selected':'' ?>>Particular</option>
    <option value="convenio" <?= $ag['tipo_consulta']=='convenio'?'selected':'' ?>>Convênio</option>
</select>

<button type="submit">Salvar</button>
<a href="agendamentos.php">Cancelar</a>
</form>
</div>

</body>
</html>
