<?php
session_start();
require_once 'conexao.php';
require_once 'verifica_admin.php';

$usuarioLogadoId = $_SESSION['user_id'];

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
body{background:#eef2f7;font-family:Poppins}
.container{max-width:900px;margin:40px auto;background:#fff;padding:30px;border-radius:16px}
.badge{padding:4px 10px;border-radius:20px;font-size:12px;color:#fff}
.admin{background:#e74c3c}
.colaborador{background:#3498db}
.bloqueado{color:#aaa;font-size:13px}
</style>
</head>
<body>

<div class="container">
<h2>Usuários do Sistema</h2>
<a href="novo_usuario.php">+ Novo Usuário</a>

<table width="100%">
<tr>
<th>ID</th><th>Nome</th><th>Username</th><th>Nível</th><th>Ações</th>
</tr>

<?php foreach ($usuarios as $u): 
$ehAdmin   = $u['nivel'] === 'admin';
$ehProprio = $u['id'] == $usuarioLogadoId;
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

<?php if ($ehAdmin): ?>
<span class="bloqueado">Admin protegido</span>

<?php elseif ($ehProprio): ?>
<span class="bloqueado">Usuário atual</span>

<?php else: ?>
<a href="excluir_usuario.php?id=<?= $u['id'] ?>" style="color:red"
onclick="return confirm('Excluir usuário?')">Excluir</a>
<?php endif; ?>

</td>
</tr>
<?php endforeach; ?>

</table>
</div>

</body>
</html>
