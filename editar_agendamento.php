<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pega o ID da URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: agendamentos.php");
    exit();
}

// Busca os dados do agendamento atual
$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id = :id");
$stmt->execute([':id' => $id]);
$ag = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ag) die("Agendamento não encontrado.");

// Busca serviços para o select
$servicos = $conn->query("SELECT id, nome FROM servicos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE agendamentos SET 
                paciente = :paciente, 
                email = :email, 
                telefone = :telefone, 
                data = :data, 
                hora = :hora, 
                servico_id = :servico_id, 
                tipo_consulta = :tipo, 
                observacoes = :obs 
                WHERE id = :id";
        
        $update = $conn->prepare($sql);
        $update->execute([
            ':paciente'   => $_POST['paciente'],
            ':email'      => $_POST['email'],
            ':telefone'   => $_POST['telefone'],
            ':data'       => $_POST['data'],
            ':hora'       => $_POST['hora'],
            ':servico_id' => $_POST['servico_id'],
            ':tipo'       => $_POST['tipo_consulta'],
            ':obs'        => $_POST['observacoes'],
            ':id'         => $id
        ]);

        header("Location: agendamentos.php?msg=sucesso");
        exit();
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f3f6fb; margin: 0; color: #2c3e50; }

        .agendamento-page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }

        .agendamento-container {
            width: 100%;
            max-width: 550px;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .topo { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .topo a { text-decoration: none; color: #64748b; font-size: 14px; font-weight: 500; }

        h2 { text-align: center; margin-bottom: 30px; font-size: 24px; color: #1e293b; }

        label { display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px; }

        input, select, textarea {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 16px;
            background: #f8fafc;
            transition: 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #4a6cf7;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.1);
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        .btn-salvar {
            width: 100%;
            padding: 16px;
            background: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-salvar:hover { background: #3b5be0; transform: translateY(-2px); }

        @media (max-width: 600px) {
            .agendamento-container { padding: 20px; }
            .form-row { grid-template-columns: 1fr; }
            h2 { font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="agendamento-page">
    <div class="agendamento-container">
        
        <div class="topo">
            <a href="agendamentos.php"><i class="fas fa-chevron-left"></i> Voltar</a>
            <span style="font-size: 12px; color: #94a3b8;">Editando ID #<?= $id ?></span>
        </div>

        <h2><i class="fas fa-edit"></i> Editar Agendamento</h2>

        <?php if(isset($erro)): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:10px; margin-bottom:20px; font-size:14px;">
                <?= $erro ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Paciente</label>
            <input type="text" name="paciente" value="<?= htmlspecialchars($ag['paciente']) ?>" required>

            <div class="form-row">
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($ag['email'] ?? '') ?>">
                </div>
                <div>
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($ag['telefone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Data</label>
                    <input type="date" name="data" value="<?= $ag['data'] ?>" required>
                </div>
                <div>
                    <label>Hora</label>
                    <input type="time" name="hora" value="<?= substr($ag['hora'], 0, 5) ?>" required>
                </div>
            </div>

            <label>Serviço</label>
            <select name="servico_id" required>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $ag['servico_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tipo de Consulta</label>
            <select name="tipo_consulta" required>
                <option value="particular" <?= $ag['tipo_consulta'] == 'particular' ? 'selected' : '' ?>>Particular</option>
                <option value="convenio" <?= $ag['tipo_consulta'] == 'convenio' ? 'selected' : '' ?>>Convênio</option>
                <option value="retorno" <?= $ag['tipo_consulta'] == 'retorno' ? 'selected' : '' ?>>Retorno</option>
            </select>

            <label>Observações</label>
            <textarea name="observacoes"><?= htmlspecialchars($ag['observacoes'] ?? '') ?></textarea>

            <button type="submit" class="btn-salvar">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </form>

    </div>
</div>

</body>
</html>
