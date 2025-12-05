<?php
if (!isset($titulo)) { $titulo = "Sistema de Agendamentos"; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= $titulo ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* =============================
   ESTILO GLOBAL DO SISTEMA
============================= */
body {
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background: #f4f6fb;
    display: flex;
}

/* MENU LATERAL */
.sidebar {
    width: 240px;
    background: #2d3436;
    color: #fff;
    min-height: 100vh;
    padding: 30px 20px;
    position: fixed;
    left: 0;
    top: 0;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 22px;
    letter-spacing: 1px;
}

.sidebar a {
    display: block;
    padding: 12px;
    margin-bottom: 12px;
    text-decoration: none;
    color: #fff;
    background: #636e72;
    border-radius: 8px;
    transition: .25s;
}

.sidebar a:hover {
    background: #0984e3;
}

/* CONTEÚDO */
.content {
    margin-left: 260px;
    padding: 30px;
    width: calc(100% - 260px);
}

/* TÍTULOS */
h1, h2 {
    color: #2d3436;
}

/* CARDS DO DASHBOARD */
.card-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

.card h3 {
    margin: 0 0 10px;
    font-size: 20px;
}

/* BOTÕES */
.btn {
    padding: 10px 18px;
    text-decoration: none;
    border-radius: 8px;
    color: #fff;
    background: #0984e3;
    transition: .25s;
}

.btn:hover {
    background: #086fc2;
}

.btn-small {
    padding: 7px 12px;
    font-size: 13px;
}

/* TABELAS */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table, th, td {
    border-bottom: 1px solid #dfe6e9;
}

th, td {
    padding: 12px;
}

th {
    background: #f0f3fa;
    font-weight: bold;
}

/* FORMULÁRIOS */
.form-container {
    max-width: 550px;
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    margin-top: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

.form-group {
    margin-bottom: 18px;
}

input, select, textarea {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #b2bec3;
    outline: none;
    font-size: 15px;
}

button {
    padding: 14px;
    border-radius: 10px;
    border: none;
    font-size: 16px;
    background: #0984e3;
    color: #fff;
    cursor: pointer;
    width: 100%;
    transition: .25s;
}

button:hover {
    background: #086fc2;
}

/* RESPONSIVIDADE */
@media (max-width: 768px) {
    .sidebar {
        width: 180px;
    }
    .content {
        margin-left: 200px;
    }
}

@media (max-width: 550px) {
    .sidebar {
        display: none;
    }
    .content {
        margin-left: 0;
        width: 100%;
    }
}
</style>
</head>
<body>

<div class="sidebar">
    <h2>AW Agenda</h2>
    <a href="index.php">Dashboard</a>
    <a href="novo_agendamento.php">Novo Agendamento</a>
    <a href="agendamentos.php">Agendamentos</a>
    <a href="servicos.php">Serviços</a>
    <a href="usuarios.php">Usuários</a>
    <a href="logout.php" style="background:#d63031;">Sair</a>
</div>

<div class="content">
