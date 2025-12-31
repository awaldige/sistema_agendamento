<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Busca serviços (SEM preco)
$sql = "SELECT id, nome, descricao, created_at
        FROM servicos
        ORDER BY nome";

$servicos = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Serviços</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 15px;
            background: #4a6cf7;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            margin-left: 6px;
        }

        .btn.menu {
            background: #7f8c8d;
        }

        .btn:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
        }

        th {
            background: #f4f6fb;
        }

        tr:nth-child(even) {
            background: #fafbfe;
        }

        .acoes a {
            margin-right: 10px;
            text-decoration: none;
            font-weight: 500;
            color: #4a6cf7;
        }

        .acoes .excluir {
            color: #e74c3c;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h2>Serviços</h2>
        <div>
            <a href="index.php" class="btn menu">← Menu</a>
            <a href="novo_servico.php" class="btn">+ Novo Serviço</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>

        <?php if (!$servicos): ?>
            <tr>
                <td colspan="5">Nenhum serviço encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($servicos as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['nome']) ?></td>
                    <td><?= htmlspecialchars($s['descricao']) ?></td>
                    <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                    <td class="acoes">
                        <a href="editar_servico.php?id=<?= $s['id'] ?>">Editar</a>
                        <a href="excluir_servico.php?id=<?= $s['id'] ?>"
                           class="excluir"
                           onclick="return confirm('Deseja realmente excluir este serviço?')">
                           Excluir
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

</div>

</body>
</html>
