<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Usuário logado
$usuarioLogadoId = $_SESSION['user_id'];
$nivelLogado     = $_SESSION['user_nivel'] ?? 'colaborador';

// Busca usuários (AGORA COM NIVEL)
$sql = "SELECT id, nome, username, nivel, created_at
        FROM usuarios
        ORDER BY nome";

$usuarios = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
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

        .acoes-topo {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 10px 15px;
            background: #4a6cf7;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
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

        .bloqueado {
            color: #aaa;
            font-size: 13px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.admin {
            background: #fdecea;
            color: #c0392b;
        }

        .badge.colaborador {
            background: #ecf4ff;
            color: #2c3e50;
        }

        .sucesso {
            background: #eafaf1;
            border-left: 4px solid #2ecc71;
            padding: 12px;
            color: #27ae60;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h2>Usuários do Sistema</h2>

        <div class="acoes-topo">
            <a href="index.php" class="btn menu">← Menu</a>

            <?php if ($nivelLogado === 'admin'): ?>
                <a href="novo_usuario.php" class="btn">+ Novo Usuário</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="sucesso">Usuário cadastrado com sucesso!</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Username</th>
                <th>Nível</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>

        <?php if (!$usuarios): ?>
            <tr>
                <td colspan="6">Nenhum usuário encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($usuarios as $u): ?>

                <?php
                    $ehAdminSistema = ($u['username'] === 'admin');
                    $ehProprio      = ($u['id'] == $usuarioLogadoId);
                ?>

                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td>
                        <span class="badge <?= $u['nivel'] ?>">
                            <?= ucfirst($u['nivel']) ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td class="acoes">

                        <?php if ($nivelLogado !== 'admin'): ?>
                            <span class="bloqueado">Sem permissão</span>

                        <?php else: ?>
                            <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>

                            <?php if ($ehAdminSistema): ?>
                                <span class="bloqueado">Admin protegido</span>

                            <?php elseif ($ehProprio): ?>
                                <span class="bloqueado">Usuário atual</span>

                            <?php else: ?>
                                <a href="excluir_usuario.php?id=<?= $u['id'] ?>"
                                   class="excluir"
                                   onclick="return confirm('Deseja realmente excluir este usuário?')">
                                   Excluir
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                    </td>
                </tr>

            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

</div>

</body>
</html>
