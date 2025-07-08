<?php
include 'conexao.php';
include_once 'notificar.php';

$email = trim($_POST['email']);

$sql = "SELECT id, nome FROM jogadores WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $codigo = rand(100000, 999999);
    $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $update = $conn->prepare("UPDATE jogadores SET token_recuperacao = ?, token_expira = ? WHERE id = ?");
    $update->bind_param("ssi", $codigo, $expira, $usuario['id']);
    $update->execute();

    $mensagem = "<p>Olá <strong>{$usuario['nome']}</strong>,</p>
    <p>Você solicitou a redefinição de senha. Use o seguinte código:</p>
    <h2 style='color:blue;'>$codigo</h2>
    <p>Este código é válido até <strong>" . date("H:i", strtotime($expira)) . "</strong>.</p>";

    enviarNotificacao($email, "Código para redefinir senha", $mensagem);
    header("Location: verificar_codigo_senha.php?email=" . urlencode($email));
    exit();
} else {
    echo "<script>alert('E-mail não encontrado.'); window.location.href='esqueci_senha.html';</script>";
}
