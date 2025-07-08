<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function enviarNotificacao($para, $assunto, $mensagem) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // ou o que estiver usando
        $mail->SMTPAuth = true;
        $mail->Username = 'kevin325343@gmail.com'; // altere aqui
        $mail->Password = 'xkefuegjkqwxpdvw'; // altere aqui
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Configurações do e-mail
        $mail->setFrom('kevin325343@gmail.com', 'Show do Programador');
        $mail->addAddress($para);
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body = $mensagem;

        $mail->send();
    } catch (Exception $e) {
        // Apenas logar ou ignorar
        error_log("Erro ao enviar e-mail de notificação: {$mail->ErrorInfo}");
    }
}
