<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT id, nome, descricao, created_at FROM servicos ORDER BY nome";
$servicos = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Serviços | Sistema Médico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            margin: 0; padding: 0;
            background: #f4f7f9;
            font-family: "Poppins", sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }

        h2 { margin: 0; color: #2c3e50; font-size: 24px; }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 10px;
            flex-wrap: wrap; /* Permite quebrar linha no celular */
        }

        .btn-group { display: flex; gap: 8px; }

        .btn {
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-primary { background: #4a6cf7; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn:hover { opacity: 0.85; transform: translateY(-1px); }

        /* ================ TABELA RESPONSIVA ================ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #edf2f7;
        }

        td {
            padding: 15px;
            color: #334155;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }

        .acoes { display: flex; gap: 15px; }
        .acoes a { text-decoration: none; font-weight: 600; font-size: 13px; }
        .btn-edit { color: #4a6cf7; }
        .btn-delete { color: #ef4444; }

        /* MEDIA QUERY PARA CELULAR */
        @media (max-width: 768px) {
            .container { margin: 10px; padding: 15px; border-radius: 12px; }
            
            /* Esconde o cabeçalho original da tabela */
            thead { display: none; }

            tr {
                display: block;
                background: #fff;
                border: 1px solid #edf2f7;
                border-radius: 12px;
                margin-bottom: 15px;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            }

            td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                border-bottom: 1px solid #f1f5f9;
                padding: 10px 5px;
            }

            td:last-child { border-bottom: none; }

            /* Insere o nome da coluna antes do valor usando o data-label */
            td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                font-size: 11px;
                text-align: left;
            }

            .acoes { justify-content: flex-end; width: 100%; }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h2><i class="fas fa-briefcase"></i> Serviços</h2>
        <div class="btn-group">
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Menu</a>
            <a href="novo_servico.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Serviço</a>
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
                <td colspan="5" style="text-align: center; padding: 30px;">Nenhum serviço encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($servicos as $s): ?>
                <tr>
                    <td data-label="ID">#<?= $s['id'] ?></td>
                    <td data-label="Nome"><strong><?= htmlspecialchars($s['nome']) ?></strong></td>
                    <td data-label="Descrição"><?= htmlspecialchars($s['descricao']) ?></td>
                    <td data-label="Criado em"><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                    <td data-label="Ações" class="acoes">
                        <a href="editar_servico.php?id=<?= $s['id'] ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="excluir_servico.php?id=<?= $s['id'] ?>" 
                           class="btn-delete"
                           onclick="return confirm('Deseja realmente excluir este serviço?')">
                            <i class="fas fa-trash"></i> Excluir
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
