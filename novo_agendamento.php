<?php
session_start();
require_once 'conexao.php';

// Proteção correta
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Carregar serviços
$stmt = $conn->query("SELECT id, nome FROM servicos ORDER BY nome ASC");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tipos de consulta
$tiposConsulta = [
    'particular' => 'Particular',
    'convenio'   => 'Convênio'
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Agendamento</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    margin: 0;
    padding: 0;
    background: #f4f6fb;
    font-family: "Poppins", sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* CARD */
.container {
    width: 100%;
    max-width: 520px;
    background: #fff;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    animation: fadeIn .4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2c3e50;
    font-weight: 600;
}

.form-group {
    margin-bottom: 18px;
    position: relative;
}

input, select, textarea {
    width: 100%;
    padding: 14px;
    border: 1px solid #d0d7e2;
    border-radius: 10px;
    outline: none;
    font-size: 15px;
    background: #f9fbff;
    transition: .25s;
}

input:focus, select:focus, textarea:focus {
    border-color: #4a6cf7;
    box-shadow: 0 0 0 3px rgba(74,108,247,0.15);
}

label {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    background: #fff;
    padding: 0 6px;
    font-size: 15px;
    color: #6b7a90;
    pointer-events: none;
    transition: .25s;
}

input:focus + label,
input:not(:placeholder-shown) + label,
textarea:focus + label,
textarea:not(:placeholder-shown) + label,
select:focus + label,
select:not([value=""]) + label {
    top: -9px;
    font-size: 12px;
    color: #4a6cf7;
}

/* BOTÃO */
button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: #4a6cf7;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: .25s;
    margin-top: 10px;
}

button:hover {
    background: #2649f5;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(74,108,247,0.25);
}
</style>
</head>

<body>

<div class="container">
    <h2>Novo Agendamento</h2>

    <form action="salvar_agendamento.php" method="POST">

        <!-- Paciente -->
        <div class="form-group">
            <input type="text" name="paciente" required placeholder=" ">
            <label>Nome do Paciente</label>
        </div>

        <!-- Email -->
        <div class="form-group">
            <input type="email" name="email" required placeholder=" ">
            <label>Email</label>
        </div>

        <!-- Telefone -->
        <div class="form-group">
            <input type="tel" name="telefone" required placeholder=" ">
            <label>Telefone</label>
        </div>

        <!-- Serviço -->
        <div class="form-group">
            <select name="servico" required>
                <option value="" disabled selected>Selecione um serviço</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Serviço</label>
        </div>

        <!-- Tipo de consulta -->
        <div class="form-group">
            <select name="tipo_consulta" required>
                <option value="" disabled selected>Selecione o tipo</option>
                <?php foreach ($tiposConsulta as $valor => $texto): ?>
                    <option value="<?= $valor ?>"><?= $texto ?></option>
                <?php endforeach; ?>
            </select>
            <label>Tipo de Consulta</label>
        </div>

        <!-- Data -->
        <div class="form-group">
            <input type="date" name="data" required placeholder=" ">
            <label>Data</label>
        </div>

        <!-- Hora -->
        <div class="form-group">
            <input type="time" name="hora" required placeholder=" ">
            <label>Hora</label>
        </div>

        <!-- Observações -->
        <div class="form-group">
            <textarea name="observacoes" placeholder=" " rows="3"></textarea>
            <label>Observações (opcional)</label>
        </div>

        <button type="submit">Salvar Agendamento</button>

    </form>

</div>

</body>
</html>
