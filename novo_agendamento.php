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
<title>Novo Agendamento | Sistema Médico</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* =========================
   BASE & RESET
========================= */
* {
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
    -webkit-tap-highlight-color: transparent;
}

body {
    margin: 0;
    background: #f3f6fb;
    color: #2c3e50;
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
    max-width: 550px;
    background: #fff;
    padding: 32px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

/* =========================
   HEADER
========================= */
.topo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.topo a {
    text-decoration: none;
    color: #64748b;
    font-size: 14px;
    font-weight: 500;
    transition: 0.2s;
}

.topo a:hover { color: #4a6cf7; }

h2 {
    margin: 0 0 25px 0;
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    text-align: center;
}

/* =========================
   FORMULÁRIO
========================= */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 8px;
    margin-top: 5px;
}

input, select, textarea {
    width: 100%;
    padding: 14px 16px;
    margin-bottom: 18px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    font-size: 16px; /* Evita zoom automático no iOS */
    color: #334155;
    background: #f8fafc;
    transition: all 0.3s ease;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #4a6cf7;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.1);
}

textarea {
    resize: none;
    min-height: 100px;
}

button {
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
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

button:hover {
    background: #3b5be0;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
}

/* =========================
   MOBILE
========================= */
@media (max-width: 600px) {
    .agendamento-page {
        padding: 10px;
    }

    .agendamento-container {
        padding: 20px;
        border-radius: 16px;
    }

    .form-row {
        grid-template-columns: 1fr; /* Empilha data e hora em telas muito pequenas */
        gap: 0;
    }
    
    h2 { font-size: 20px; }
}
</style>
</head>

<body>

<div class="agendamento-page">
    <div class="agendamento-container">

        <div class="topo">
            <a href="index.php"><i class="fas fa-chevron-left"></i> Voltar ao Menu</a>
        </div>

        <h2>Novo Agendamento</h2>

        <form method="POST" action="salvar_agendamento.php">

            <label><i class="fas fa-user"></i> Nome do Paciente</label>
            <input type="text" name="paciente" placeholder="Digite o nome completo" required>

            <div class="form-row">
                <div>
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="exemplo@email.com">
                </div>
                <div>
                    <label><i class="fas fa-phone"></i> Telefone</label>
                    <input type="tel" name="telefone" placeholder="(00) 00000-0000">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label><i class="fas fa-calendar"></i> Data</label>
                    <input type="date" name="data" required>
                </div>
                <div>
                    <label><i class="fas fa-clock"></i> Hora</label>
                    <input type="time" name="hora" required>
                </div>
            </div>

            <label><i class="fas fa-hand-holding-medical"></i> Serviço</label>
            <select name="servico_id" required>
                <option value="">Selecione o serviço</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label><i class="fas fa-file-invoice-dollar"></i> Tipo de Consulta</label>
            <select name="tipo_consulta" required>
                <option value="">Selecione</option>
                <option value="particular">Particular</option>
                <option value="convenio">Convênio</option>              
            </select>

            <label><i class="fas fa-comment-alt"></i> Observações</label>
            <textarea name="observacoes" placeholder="Alguma observação importante?"></textarea>

            <button type="submit">
                <i class="fas fa-check-circle"></i> Confirmar Agendamento
            </button>

        </form>

    </div>
</div>

</body>
</html>

