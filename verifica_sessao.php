<?php
// Define tempo limite em segundos (10 min = 600s)
$tempoLimite = 600;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $tempoInativo = time() - $_SESSION['LAST_ACTIVITY'];

    if ($tempoInativo > $tempoLimite) {
        session_unset();     // Limpa variáveis da sessão
        session_destroy();   // Destrói a sessão

        echo "<script>alert('Sessão expirada por inatividade. Faça login novamente.'); window.location.href='login.html';</script>";
        exit();
    }
}

// Atualiza hora da última atividade
$_SESSION['LAST_ACTIVITY'] = time();
?>
