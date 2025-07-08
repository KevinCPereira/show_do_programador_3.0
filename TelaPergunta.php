<?php
session_start();
if (!isset($_SESSION['LOGGED_IN'])) {
    header("Location: login.html");
    exit();
}
include 'verifica_sessao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

include 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];

// Seleciona uma pergunta que o usuário ainda NÃO acertou
$sql = "
    SELECT p.id AS pergunta_id, p.enunciado, c.nome AS categoria
    FROM perguntas p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.id NOT IN (
        SELECT pergunta_id FROM respostas 
        WHERE usuario_id = $usuario_id AND acertou = 1
    )
    ORDER BY RAND()
    LIMIT 1
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $pergunta = $result->fetch_assoc();
  $pergunta_id = $pergunta['pergunta_id'];

  $sql_alt = "SELECT id, texto FROM alternativas WHERE pergunta_id = $pergunta_id";
  $result_alt = $conn->query($sql_alt);
} else {
  // Todas as perguntas foram respondidas corretamente!
  echo '
  <!DOCTYPE html>
  <html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="refresh" content="5;url=telaInicial.php">
    <title>Parabéns!</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white min-h-screen flex items-center justify-center p-6">
    <div class="bg-green-500 text-black font-bold text-xl rounded-2xl p-8 text-center shadow-2xl">
      🎉 Parabéns! Você respondeu corretamente a todas as perguntas disponíveis!<br><br>
      Você será redirecionado ao menu principal...
    </div>
  </body>
  </html>';
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pergunta</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 min-h-screen text-white flex items-center justify-center p-6">

  <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl shadow-2xl w-full max-w-2xl text-center">
    <div class="mb-8">
      <h2 class="text-xl font-semibold mb-2">Categoria: <?= $pergunta['categoria'] ?></h2>
      <p class="text-2xl font-bold"><?= $pergunta['enunciado'] ?></p>
    </div>

    <form method="post" action="verifica_resposta.php">
      <div class="grid grid-cols-1 gap-4">
        <?php while ($alt = $result_alt->fetch_assoc()): ?>
          <label>
            <input type="radio" name="alternativa" value="<?= $alt['id'] ?>" class="hidden peer" required>
            <div class="bg-yellow-400 hover:bg-yellow-300 text-black font-bold py-3 px-6 rounded-xl transition-all cursor-pointer peer-checked:ring-4 ring-yellow-300">
              <?= $alt['texto'] ?>
            </div>
          </label>
        <?php endwhile; ?>
      </div>

      <input type="hidden" name="pergunta_id" value="<?= $pergunta_id ?>">

      <div class="mt-6">
        <button type="submit" class="bg-green-500 hover:bg-green-400 text-white font-bold py-2 px-6 rounded-xl">Responder</button>
      </div>
    </form>
  </div>

</body>
</html>
