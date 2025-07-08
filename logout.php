<?php
session_start();
include 'conexao.php';
include 'auditar.php';
include_once 'notificar.php';

// Registra a ação de logout e envia e-mail se o usuário estiver logado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $usuario_email = $_SESSION['usuario_email'];
    $usuario_nome = $_SESSION['usuario_nome'];

    registrarAcao($conn, $usuario_id, 'Logout', 'Usuário realizou logout manualmente.');

    // Envia notificação por e-mail
    $mensagem = "<p>Olá <strong>$usuario_nome</strong>,</p>
    <p>Você realizou logout da sua conta no dia <strong>" . date("d/m/Y H:i") . "</strong>.</p>";
    enviarNotificacao($usuario_email, 'Logout realizado com sucesso', $mensagem);
}

session_unset();
session_destroy();

header("Location: telaInicial.php?msg=logout");
exit();
?>
