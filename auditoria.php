<?php
session_start();
if (!isset($_SESSION['LOGGED_IN'])) {
    header("Location: login.html");
    exit();
}
include 'conexao.php';

// Redireciona se não for professor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header("Location: telaInicial.php");
    exit();
}

// Consulta as ações registradas
$sql = "
    SELECT a.*, j.nome 
    FROM auditoria a 
    JOIN jogadores j ON a.usuario_id = j.id 
    ORDER BY a.data_hora DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auditoria do Sistema</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white min-h-screen p-6">
  <div class="max-w-5xl mx-auto bg-white/10 backdrop-blur-md p-8 rounded-2xl shadow-xl">
    <h1 class="text-3xl font-bold mb-6 text-center">📋 Log de Auditoria</h1>

    <table class="w-full text-left border-collapse bg-white/20 text-white text-sm rounded-xl overflow-hidden">
      <thead class="bg-white/30 font-semibold text-white">
        <tr>
          <th class="py-3 px-4">Usuário</th>
          <th class="py-3 px-4">Ação</th>
          <th class="py-3 px-4">Detalhes</th>
          <th class="py-3 px-4">Data/Hora</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($linha = $result->fetch_assoc()): ?>
          <tr class="border-t border-white/20 hover:bg-white/10">
            <td class="py-2 px-4"><?= htmlspecialchars($linha['nome']) ?></td>
            <td class="py-2 px-4"><?= htmlspecialchars($linha['acao']) ?></td>
            <td class="py-2 px-4"><?= htmlspecialchars($linha['detalhes']) ?></td>
            <td class="py-2 px-4"><?= date('d/m/Y H:i:s', strtotime($linha['data_hora'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="mt-6 text-center">
      <a href="telaInicial.php" class="text-yellow-300 hover:underline">← Voltar</a>
    </div>
  </div>
</body>
</html>
