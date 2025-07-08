<?php
session_start();
if (!isset($_SESSION['LOGGED_IN'])) {
    header("Location: login.html");
    exit();
}
include 'verifica_sessao.php';
include 'conexao.php';

// Garante que só professores possam acessar
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header("Location: telaInicial.php");
    exit();
}

// Busca todos os jogadores que são alunos
$sql = "SELECT id, nome FROM jogadores WHERE tipo = 'aluno' ORDER BY nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lista de Jogadores</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white min-h-screen p-6">
  <div class="max-w-3xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl">
    <h1 class="text-3xl font-bold mb-6 text-center">👨‍🎓 Lista de Jogadores</h1>

    <?php if ($result && $result->num_rows > 0): ?>
      <ul class="space-y-4">
        <?php while ($aluno = $result->fetch_assoc()): ?>
          <li class="bg-white/20 hover:bg-white/30 rounded-xl px-4 py-3 transition">
            <a href="perfil_aluno.php?id=<?= $aluno['id'] ?>" class="font-semibold text-lg text-yellow-300 hover:underline">
              <?= htmlspecialchars($aluno['nome']) ?>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p class="text-white/70 text-center">Nenhum aluno encontrado.</p>
    <?php endif; ?>

    <div class="mt-8 text-center">
      <a href="telaInicial.php" class="text-yellow-300 underline hover:text-yellow-200">← Voltar</a>
    </div>
  </div>
</body>
</html>
