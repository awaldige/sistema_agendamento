<?php
// servico_delete.php
require_once 'auth.php';
require_once 'conexao.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // opcional: verificar se serviço está ligado a agendamentos antes de excluir
    $stmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE servico_id = :id");
    $stmt->execute([':id'=>$id]);
    $count = (int)$stmt->fetchColumn();
    if ($count > 0) {
        $_SESSION['flash'] = 'Não é possível excluir: existem agendamentos vinculados a este serviço.';
    } else {
        $stmt = $conn->prepare("DELETE FROM servicos WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        $_SESSION['flash'] = 'Serviço excluído.';
    }
}
header('Location: servicos.php');
exit;
