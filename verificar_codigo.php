<?php
session_start();

// Verifica se o código foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigoDigitado = $_POST['codigo'] ?? '';
    $codigoGerado = $_SESSION['2fa_codigo'] ?? '';
    $tempoEnvio = $_SESSION['2fa_hora'] ?? 0;

    if (time() - $tempoEnvio > 300) {
        $erro = "⏰ O código expirou. Faça login novamente.";
        session_destroy();
    } elseif ($codigoDigitado === $codigoGerado) {
        $_SESSION['LOGGED_IN'] = true;
        header("Location: telaInicial.php");
        exit();
    } else {
        $erro = "❌ Código inválido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Verificação 2FA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-indigo-900 text-white min-h-screen flex items-center justify-center">
  <form method="POST" class="bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-xl text-center max-w-md w-full">
    <h2 class="text-2xl font-bold mb-4">🔐 Verificação em 2 Etapas</h2>
    <p class="mb-6">Digite o código que foi enviado para seu e-mail.</p>
    
    <?php if (!empty($erro)): ?>
      <div class="bg-red-500 text-white rounded-lg p-3 mb-4"><?= $erro ?></div>
    <?php endif; ?>

    <input type="text" name="codigo" maxlength="6" required placeholder="Código de 6 dígitos"
           class="text-black px-4 py-2 rounded-xl w-full text-center mb-4"/>

    <button type="submit"
            class="bg-yellow-400 hover:bg-yellow-300 text-black font-bold py-2 px-4 rounded-xl w-full">
      Verificar
    </button>
  </form>
</body>
</html>
