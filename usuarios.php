<?php
session_start();
require_once 'conexao.php';

// Verificação de segurança
if (file_exists('verifica_admin.php')) {
    require_once 'verifica_admin.php';
} else {
    die("Erro crítico: O arquivo de segurança (verifica_admin.php) não foi encontrado.");
}

$usuarioLogadoId = $_SESSION['user_id'] ?? 0;

// Busca os usuários
$sql = "SELECT id, nome, username, nivel, created_at FROM usuarios ORDER BY nome";
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#eef2f7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding:20px; color: #333; }
        .container { max-width:1000px; margin:20px auto; background:#fff; padding:30px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.05); }
        
        /* Cabeçalho e Botões */
        .header-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; font-size: 14px; }
        
        .btn-voltar { background: #64748b; color: white; }
        .btn-voltar:hover { background: #475569; }
        
        .btn-novo { background: #2ecc71; color: white; }
        .btn-novo:hover { background: #27ae60; }

        /* Tabela Responsiva */
        .table-wrapper { overflow-x: auto; }
        table { width:100%; border-collapse:collapse; min-width: 600px; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 15px; }
        tr:hover { background: #fcfcfc; }

        /* Estilos de Status */
        .badge { padding:4px 12px; border-radius:20px; font-size:11px; font-weight: bold; color:#fff; text-transform: uppercase; }
        .admin { background:#e74c3c; }
        .colaborador { background:#3498db; }
        .bloqueado { color:#94a3b8; font-size:13px; font-style:italic; }
        
        .action-links a { color: #3498db; text-decoration: none; margin-right: 10px; font-weight: 500; }
        .action-links a.delete { color: #e74c3c; }

        @media (max-width: 600px) {
            .header-actions { flex-direction: column; align-items: stretch; }
            .btn { justify-content: center; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-actions">
        <div>
            <h2 style="margin:0; color:#1e3c72;">Usuários do Sistema</h2>
            <p style="margin:5px 0 0; color:#666; font-size:14px;">Gerencie permissões e acessos.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="index.php" class="btn btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao Menu
            </a>
            <a href="novo_usuario.php" class="btn btn-novo">
                <i class="fas fa-plus"></i> Novo Usuário
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Username</th>
                    <th>Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): 
                    $ehAdmin   = ($u['nivel'] === 'admin');
                    $ehProprio = ($u['id'] == $usuarioLogadoId);
                ?>
                <tr>
                    <td><strong>#<?= $u['id'] ?></strong></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><code style="background:#f1f5f9; padding:2px 5px; border-radius:4px;"><?= htmlspecialchars($u['username']) ?></code></td>
                    <td>
                        <span class="badge <?= $u['nivel'] ?>">
                            <?= ucfirst($u['nivel']) ?>
                        </span>
                    </td>
                    <td class="action-links">
                        <a href="editar_usuario.php?id=<?= $u['id'] ?>" title="Editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>

                        <?php if ($ehAdmin): ?>
                            <span class="bloqueado" title="Administradores não podem ser excluídos por aqui">
                                <i class="fas fa-lock"></i> Protegido
                            </span>
                        <?php elseif ($ehProprio): ?>
                            <span class="bloqueado"><i class="fas fa-user"></i> Você</span>
                        <?php else: ?>
                            <a href="excluir_usuario.php?id=<?= $u['id'] ?>" class="delete" 
                               onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                <i class="fas fa-trash"></i> Excluir
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
