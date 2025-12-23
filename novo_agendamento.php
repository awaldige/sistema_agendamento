<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servicos = $conn->query("SELECT id, nome FROM servicos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Agendamento</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* =========================
   BASE
========================= */
* {
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    margin: 0;
    background: #f3f6fb;
}

/* =========================
   CONTAINER PRINCIPAL
========================= */
.agendamento-page {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 20px;
}

.agendamento-container {
    width: 100%;
    max-width: 520px;
    background: #fff;
    padding: 28px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

/* =========================
   HEADER
========================= */
.topo {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.topo a {
    text-decoration: none;
    color: #4a6cf7;
    font-weight: 500;
}

.topo h2 {
    margin: 0;
    font-size: 22px;
    color: #2c3e50;
}

/* =========================
   FORMULÁRIO
========================= */
label {
    font-size: 13px;
    font-weight: 500;
    color: #555;
}

input,
select,
textarea,
button {
    width: 100%;
    padding: 14px;
    margin-top: 6px;
    margin-bottom: 14px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 16px;
}

textarea {
    resize: none;
    min-height: 90px;
}

button {
    background: #4a6cf7;
    color: #fff;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    background: #3b5be0;
}

/* =========================
   MOBILE
========================= */
@media (max-width: 768px) {

    .agendamento-page {
        padding: 0;
    }

    .agendamento-container {
        min-height: 100vh;
        border-radius: 0;
        padding: 24px;
        box-shadow: none;
    }

    .topo h2 {
        font-size: 20px;
    }
}
</style>
</head>

<body>

<div class="agendamento-page">
    <div class="agendamento-container">

        <div class="topo">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i> Menu
            </a>
        </div>

        <h2>Novo Agendamento</h2>

        <form method="POST" action="salvar_agendamento.php">

            <label>Paciente</label>
            <input type="text" name="paciente" required>

            <label>Email</label>
            <input type="email" name="email">

            <label>Telefone</label>
            <input type="text" name="telefone">

            <label>Data</label>
            <input type="date" name="data" required>

            <label>Hora</label>
            <input type="time" name="hora" required>

            <label>Serviço</label>
            <select name="servico_id" required>
                <option value="">Selecione o serviço</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tipo de Consulta</label>
            <select name="tipo_consulta" required>
                <option value="">Selecione</option>
                <option value="particular">Particular</option>
                <option value="convenio">Convênio</option>
            </select>

            <label>Observações</label>
            <textarea name="observacoes"></textarea>

            <button type="submit">
                <i class="fas fa-save"></i> Salvar Agendamento
            </button>

        </form>

    </div>
</div>

</body>
</html>
