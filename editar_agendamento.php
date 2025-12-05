<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: agendamentos.php");
    exit();
}

$id = $_GET['id'];

// Buscar agendamento
$sql = "SELECT * FROM agendamentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header("Location: agendamentos.php");
    exit();
}

// Buscar serviços
$servicos = $conn->query("SELECT * FROM servicos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
        }

        .container {
            max-width: 650px;
            margin: 40px auto;
            background: #fff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 26px;
        }

        .voltar {
            display: inline-block;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #7f8c8d;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
        }

        .voltar:hover {
            background: #636e72;
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #2c3e50;
        }

        input, select {
            width: 100%;
            margin-top: 6px;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dfe6ed;
            background: #f8f9fc;
            font-size: 15px;
        }

        input:focus, select:focus {
            border-color: #4a6cf7;
            outline: none;
            background: #fff;
        }

        button {
            width: 100%;
            margin-top: 30px;
            padding: 14px;
            background: #4a6cf7;
            border: none;
            color: #fff;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #3453e3;
        }

        .row {
            display: flex;
            gap: 15px;
        }

        .row div {
            flex: 1;
        }
    </style>

</head>

<body>

    <div class="container">

        <a href="agendamentos.php" class="voltar"><i class="fas fa-arrow-left"></i> Voltar</a>

        <h2><i class="fas fa-edit"></i> Editar Agendamento</h2>

        <!-- IMPORTANTE: confirme se o caminho abaixo está correto -->
        <form method="post" action="salvar_edicao.php">

            <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">

            <label>Paciente</label>
            <input type="text" name="paciente" value="<?= htmlspecialchars($agendamento['paciente']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($agendamento['email']) ?>" required>

            <label>Telefone</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($agendamento['telefone']) ?>" required>

            <div class="row">
                <div>
                    <label>Data</label>
                    <input type="date" name="data" value="<?= $agendamento['data'] ?>" required>
                </div>

                <div>
                    <label>Hora</label>
                    <input type="time" name="hora" value="<?= $agendamento['hora'] ?>" required>
                </div>
            </div>

            <label>Serviço</label>
            <select name="servico_id" required>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>"
                        <?= ($s['id'] == $agendamento['servico_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tipo de Consulta</label>
            <select name="tipo_consulta" required>
                <option value="particular" <?= $agendamento['tipo_consulta'] == "particular" ? "selected" : "" ?>>
                    Particular
                </option>
                <option value="convenio" <?= $agendamento['tipo_consulta'] == "convenio" ? "selected" : "" ?>>
                    Convênio
                </option>
            </select>

            <button type="submit">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>

        </form>

    </div>

</body>

</html>
