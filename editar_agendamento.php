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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        UPDATE agendamentos SET
        paciente=:paciente, data=:data, hora=:hora, tipo_consulta=:tipo
        WHERE id=:id
    ");
    $stmt->execute([
        ':paciente' => $_POST['paciente'],
        ':data'     => $_POST['data'],
        ':hora'     => $_POST['hora'],
        ':tipo'     => $_POST['tipo_consulta'],
        ':id'       => $id
    ]);
    header("Location: agendamentos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Editar Consulta</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f3f6fb;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .container-edit {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 22px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 14px; /* Aumentado para facilitar o toque */
            border: 2px solid #edf2f7;
            border-radius: 12px;
            font-size: 16px; /* Tamanho ideal para mobile não dar zoom */
            color: #2c3e50;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #4a6cf7;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.1);
        }

        .btn-salvar {
            width: 100%;
            padding: 16px;
            background: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-salvar:hover {
            background: #3a56d4;
            transform: translateY(-2px);
        }

        .btn-cancelar {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        /* Ajustes específicos para telas muito pequenas */
        @media (max-width: 480px) {
            .container-edit {
                padding: 20px;
            }
            h2 { font-size: 18px; }
        }
    </style>
</head>
<body>

<div class="container-edit">
    <h2><i class="fas fa-calendar-day"></i> Editar Consulta</h2>

    <form method="POST">
        <div class="form-group">
            <label>Nome do Paciente</label>
            <input type="text" name="paciente" value="<?= htmlspecialchars($ag['paciente']) ?>" placeholder="Ex: João Silva" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="data" value="<?= $ag['data'] ?>" required>
            </div>
            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora" value="<?= substr($ag['hora'],0,5) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Tipo de Atendimento</label>
            <select name="tipo_consulta">
                <option value="particular" <?= $ag['tipo_consulta']=='particular'?'selected':'' ?>>Particular</option>
                <option value="convenio" <?= $ag['tipo_consulta']=='convenio'?'selected':'' ?>>Convênio</option>
                <option value="retorno" <?= $ag['tipo_consulta']=='retorno'?'selected':'' ?>>Retorno</option>
            </select>
        </div>

        <button type="submit" class="btn-salvar">Confirmar Alterações</button>
        <a href="agendamentos.php" class="btn-cancelar">Voltar sem salvar</a>
    </form>
</div>

</body>
</html>
