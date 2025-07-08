<?php
session_start();

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verifique se o usuário já está autenticado (login feito)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nome']) || !isset($_SESSION['usuario_email'])) {
    header("Location: login.html");
    exit();
}

// Gera o código aleatório de 6 dígitos
$codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// Salva o código e o tempo de envio na sessão
$_SESSION['2fa_codigo'] = $codigo;
$_SESSION['2fa_hora'] = time();

// Envia por e-mail
$destinatario = $_SESSION['usuario_email'];
$nome = $_SESSION['usuario_nome'];

$mail = new PHPMailer(true);

try {
    // Configurações SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kevin325343@gmail.com';        // <- seu e-mail
    $mail->Password   = 'xkefuegjkqwxpdvw';              // <- senha de aplicativo
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('kevin325343@gmail.com', 'Show do Milhão');
    $mail->addAddress($destinatario, $nome);

    $mail->isHTML(true);
    $mail->Subject = 'Código de verificação (2FA)';
    $mail->Body    = "<p>Olá, <strong>$nome</strong>!</p><p>Seu código de verificação é: <strong style='font-size:18px;'>$codigo</strong></p><p>Este código expira em 5 minutos.</p>";

    $mail->send();
    header("Location: verificar_codigo.php");
    exit();
} catch (Exception $e) {
    echo "<p>Erro ao enviar e-mail: {$mail->ErrorInfo}</p>";
}
