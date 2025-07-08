<?php
session_start();
include 'conexao.php'; 
include 'auditar.php';

// Tempo limite de inatividade (em segundos)
$tempoLimite = 600; // 10 minutos

// Se sessão ativa, verifica inatividade
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $tempoInativo = time() - $_SESSION['LAST_ACTIVITY'];

    if ($tempoInativo > $tempoLimite) {
        // Auditoria de logout por inatividade
        if (isset($_SESSION['usuario_id'])) {
            registrarAcao($conn, $_SESSION['usuario_id'], 'Logout', 'Sessão encerrada por inatividade.');
        }

        session_unset();
        session_destroy();
        header("Location: index.php?msg=inatividade");
        exit();
    }
}

// Atualiza hora da última atividade
$_SESSION['LAST_ACTIVITY'] = time();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Show do Milhão - Tela Principal</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white min-h-screen flex flex-col items-center justify-center p-6">

  <!-- Mensagem de sessão expirada -->
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'inatividade'): ?>
    <div class="bg-red-500 text-white px-6 py-3 rounded-xl mb-6 shadow-lg text-center max-w-lg">
      Sua sessão foi encerrada por inatividade. Faça login novamente.
    </div>
  <?php endif; ?>

  <!-- Cabeçalho com nome e sair -->
  <?php if (isset($_SESSION['usuario_id'])): ?>
    <div class="w-full flex justify-end items-center space-x-4 absolute top-4 right-6">
      <div class="w-10 h-10 rounded-full bg-yellow-300 flex items-center justify-center text-black font-bold">
        <?= strtoupper(substr($_SESSION['usuario_nome'], 0, 1)) ?>
      </div>
      <span class="text-white/80 font-medium">
        <?= $_SESSION['usuario_nome'] ?>
      </span>
      <a href="logout.php" class="text-red-400 hover:text-red-300 font-semibold underline">Sair</a>
    </div>
  <?php endif; ?>

  <main class="text-center mt-16">
    <h1 class="text-4xl font-bold mb-10">🎮 Show do Milhão - Computação</h1>
    <div class="flex flex-col items-center space-y-5">

      <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] === 'aluno'): ?>
        <!-- Botão Jogar (apenas para alunos) -->
        <a href="TelaPergunta.php" class="w-full max-w-xs">
          <button class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-bold py-3 px-6 rounded-2xl shadow-lg transition-all">
            Jogar
          </button>
        </a>
      <?php endif; ?>

      <?php if (isset($_SESSION['usuario_id'])): ?>
        <!-- Botão Perfil -->
        <a href="<?= ($_SESSION['usuario_tipo'] === 'professor') ? 'listar_jogadores.php' : 'perfil.php' ?>" class="w-full max-w-xs">
          <button class="w-full bg-purple-500 hover:bg-purple-400 font-bold py-3 px-6 rounded-2xl shadow-lg transition-all">
            Perfil do Jogador
          </button>
        </a>

        <?php if ($_SESSION['usuario_tipo'] === 'professor'): ?>
          <!-- Botão Auditoria exclusivo para professor -->
          <a href="auditoria.php" class="w-full max-w-xs">
            <button class="w-full bg-red-500 hover:bg-red-400 font-bold py-3 px-6 rounded-2xl shadow-lg transition-all">
              Ver Auditoria
            </button>
          </a>
        <?php endif; ?>

      <?php else: ?>
        <!-- Botão Login -->
        <a href="login.html" class="w-full max-w-xs">
          <button class="w-full bg-blue-500 hover:bg-blue-400 font-bold py-3 px-6 rounded-2xl shadow-lg transition-all">
            Login
          </button>
        </a>
      <?php endif; ?>

    </div>
  </main>
</body>
</html>
