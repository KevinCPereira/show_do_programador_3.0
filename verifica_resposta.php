<?php
session_start();
if (!isset($_SESSION['LOGGED_IN'])) {
    header("Location: login.html");
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

include 'conexao.php';

$mensagem = "";
$cor = "";
$acertou = false;

if (isset($_POST['alternativa'])) {
    $alternativa_id = $_POST['alternativa'];
    $pergunta_id = $_POST['pergunta_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verificar se a alternativa é correta
    $sql = "SELECT correta FROM alternativas WHERE id = $alternativa_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $resposta = $result->fetch_assoc();
        $acertou = ($resposta['correta'] === 'sim');

        // Salvar no banco
        $acerto_int = $acertou ? 1 : 0;
        $conn->query("INSERT INTO respostas (usuario_id, pergunta_id, acertou) VALUES ($usuario_id, $pergunta_id, $acerto_int)");

        // Mensagem e cor
        if ($acertou) {
            $mensagem = "✅ Resposta correta!";
            $cor = "bg-green-500";
        } else {
            $mensagem = "❌ Resposta incorreta! Você será redirecionado ao menu...";
            $cor = "bg-red-500";
        }
    } else {
        $mensagem = "Erro ao buscar alternativa.";
        $cor = "bg-gray-500";
    }
} else {
    $mensagem = "Nenhuma alternativa selecionada.";
    $cor = "bg-gray-500";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Resultado</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <?php if (!$acertou): ?>
    <meta http-equiv="refresh" content="4;url=telaInicial.php">
  <?php endif; ?>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 min-h-screen flex items-center justify-center text-white">

  <div class="text-center bg-white/10 backdrop-blur-lg p-8 rounded-2xl shadow-2xl w-full max-w-lg">
    <h1 class="text-3xl font-bold mb-6">Resultado</h1>

    <div class="<?= $cor ?> text-white font-semibold text-xl py-4 px-6 rounded-xl mb-6">
      <?= $mensagem ?>
    </div>

    <?php if ($acertou): ?>
      <a href="TelaPergunta.php">
        <button class="bg-yellow-400 hover:bg-yellow-300 text-black font-bold py-3 px-6 rounded-xl transition-all">
          Próxima Pergunta
        </button>
      </a>
    <?php endif; ?>
  </div>

</body>
</html>
