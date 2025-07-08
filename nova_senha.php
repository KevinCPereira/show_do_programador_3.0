<?php
session_start();
include 'conexao.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $nova_senha = trim($_POST['nova_senha']);
    $codigo = trim($_POST['codigo']);

    if (empty($email) || empty($nova_senha) || empty($codigo)) {
        echo "<script>alert('Preencha todos os campos.'); window.location.href='esqueci_senha.html';</script>";
        exit();
    }

    $sql = "SELECT id FROM jogadores WHERE email = ? AND codigo_recuperacao = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $codigo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();

        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE jogadores SET senha = ?, codigo_recuperacao = NULL WHERE id = ?");
        $update->bind_param("si", $hash, $id);
        $update->execute();
        $update->close();

        // Enviar notificação por e-mail
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kevin325343@gmail.com'; // seu email
            $mail->Password   = 'xkefuegjkqwxpdvw';       // sua senha de app
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('kevin325343@gmail.com', 'Show do Milhão');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Senha alterada com sucesso';
            $mail->Body    = 'Olá! Informamos que sua senha foi alterada com sucesso. Se você não reconhece essa ação, entre em contato imediatamente.';

            $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de notificação: {$mail->ErrorInfo}");
        }

        echo "<script>alert('Senha redefinida com sucesso! Faça login.'); window.location.href='login.html';</script>";
        exit();
    } else {
        echo "<script>alert('Código inválido.'); window.location.href='esqueci_senha.html';</script>";
        exit();
    }
}
?>
