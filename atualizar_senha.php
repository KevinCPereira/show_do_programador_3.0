<?php
include 'conexao.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$email = trim($_POST['email']);
$senha = trim($_POST['senha']);

if (strlen($senha) < 5 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/\d/', $senha)) {
    echo "<script>alert('A senha deve ter ao menos 5 caracteres, incluindo letras e números.'); window.history.back();</script>";
    exit();
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

// Atualiza senha no banco
$sql = "UPDATE jogadores SET senha = ?, token_recuperacao = NULL, token_expira = NULL WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hash, $email);

if ($stmt->execute()) {
    // Enviar e-mail de notificação
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kevin325343@gmail.com';       // <--- coloque seu email aqui
        $mail->Password   = 'xkefuegjkqwxpdvw';             // <--- coloque sua senha de app aqui
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('kevin325343@gmail.com', 'Show do Milhão');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = '🔒 Sua senha foi alterada';
        $mail->Body    = 'Olá! Sua senha foi alterada com sucesso. Se você não reconhece essa ação, entre em contato com o suporte imediatamente.';

        $mail->send();
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
    }

    echo "<script>alert('Senha atualizada com sucesso! Faça login.'); window.location.href='login.html';</script>";
} else {
    echo "<script>alert('Erro ao atualizar senha.'); window.location.href='esqueci_senha.html';</script>";
}

$stmt->close();
$conn->close();
?>
