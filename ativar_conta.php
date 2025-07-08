<?php
include 'conexao.php';

$codigo = $_GET['codigo'] ?? '';

if (empty($codigo)) {
    echo "<script>alert('Código de ativação inválido.'); window.location.href='login.html';</script>";
    exit();
}

// Procura o usuário com o código
$sql = "SELECT id FROM jogadores WHERE codigo_ativacao = ? AND ativo = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $id = $usuario['id'];

    // Ativa a conta e remove o código
    $sqlUpdate = "UPDATE jogadores SET ativo = 1, codigo_ativacao = NULL WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("i", $id);

    if ($stmtUpdate->execute()) {
        echo "<script>alert('Conta ativada com sucesso! Faça login.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Erro ao ativar a conta.'); window.location.href='login.html';</script>";
    }
    $stmtUpdate->close();
} else {
    echo "<script>alert('Código inválido ou conta já ativada.'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
