<?php
session_start();
require_once 'conexao.php';

// Certifique-se que este arquivo existe na mesma pasta
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
    <title>Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{background:#eef2f7;font-family:sans-serif;margin:0;padding:20px}
        .container{max-width:900px;margin:40px auto;background:#fff;padding:30px;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.1)}
        table{width:100%;border-collapse:collapse;margin-top:20px}
        th, td{text-align:left;padding:12px;border-bottom:1px solid #eee}
        .badge{padding:4px 10px;border-radius:20px;font-size:12px;color:#fff;display:inline-block}
        .admin{background:#e74c3c}
        .colaborador{background:#3498db}
        .bloqueado{color:#aaa;font-size:13px;font-style:italic}
        a{text-decoration:none;color:#3498db}
        .btn-novo{background:#2ecc71;color:#fff;padding:10px 15px;border-radius:5px;display:inline-block;margin-bottom:15px}
    </style>
</head>
<body>

<div class="container">
    <h2>Usuários do Sistema</h2>
    <a href="novo_usuario.php" class="btn-novo">+ Novo Usuário</a>

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
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td>
                    <span class="badge <?= $u['nivel'] ?>">
                        <?= ucfirst($u['nivel']) ?>
                    </span>
                </td>
                <td>
                    <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                    |
                    <?php if ($ehAdmin): ?>
                        <span class="bloqueado">Admin protegido</span>
                    <?php elseif ($ehProprio): ?>
                        <span class="bloqueado">Você</span>
                    <?php else: ?>
                        <a href="excluir_usuario.php?id=<?= $u['id'] ?>" style="color:red" 
                           onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
