<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function enviarLinkAtivacao($destinatario, $nome, $codigo) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kevin325343@gmail.com'; // <- seu Gmail
        $mail->Password   = 'xkefuegjkqwxpdvw';    // <- senha de aplicativo gerada
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('kevin325343@gmail.com', 'Show do Milhão');
        $mail->addAddress($destinatario, $nome);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Ativação de Conta - Show do Milhão';
        $link = "http://localhost/show_do_programador/ativar_conta.php?codigo=$codigo";

        $mail->Body = "
            <h2>Bem-vindo ao Show do Milhão!</h2>
            <p>Olá <strong>$nome</strong>,</p>
            <p>Para ativar sua conta, clique no botão abaixo ou copie o link no navegador:</p>
            <a href='$link' style='background:#22c55e;color:#fff;padding:10px 20px;border-radius:5px;text-decoration:none;'>Ativar Conta</a>
            <p><small>Se você não fez esse cadastro, ignore este e-mail.</small></p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "<script>alert('Erro ao enviar e-mail: {$mail->ErrorInfo}');</script>";
    }
}
?>
