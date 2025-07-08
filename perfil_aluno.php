<?php
session_start();
include 'verifica_sessao.php';
include 'conexao.php';

// Garante que só o professor pode acessar
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header("Location: telaInicial.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Aluno não especificado.");
}

$aluno_id = intval($_GET['id']);

// Busca nome do aluno
$busca = $conn->query("SELECT nome FROM jogadores WHERE id = $aluno_id AND tipo = 'aluno'");
if ($busca->num_rows === 0) {
    die("Aluno não encontrado.");
}
$aluno_nome = $busca->fetch_assoc()['nome'];

// Total respondido
$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM respostas WHERE usuario_id = $aluno_id");
$total = $totalQuery->fetch_assoc()['total'];

// Total de acertos
$acertosQuery = $conn->query("SELECT COUNT(*) AS acertos FROM respostas WHERE usuario_id = $aluno_id AND acertou = 1");
$acertos = $acertosQuery->fetch_assoc()['acertos'];
$porcentagem = ($total > 0) ? round(($acertos / $total) * 100) : 0;

// Por categoria
$categoriaQuery = $conn->query("
  SELECT c.nome AS categoria, COUNT(*) AS total, 
         SUM(r.acertou) AS acertos
  FROM respostas r
  JOIN perguntas p ON r.pergunta_id = p.id
  JOIN categorias c ON p.categoria_id = c.id
  WHERE r.usuario_id = $aluno_id
  GROUP BY c.nome
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Perfil do Aluno</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white min-h-screen p-6">

  <div class="max-w-3xl mx-auto bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl">
    <h1 class="text-3xl font-bold text-center mb-6">📊 Perfil do Aluno: <?= htmlspecialchars($aluno_nome) ?></h1>

    <p><strong>Total de Perguntas Respondidas:</strong> <?= $total ?></p>
    <p><strong>Total de Acertos:</strong> <?= $acertos ?></p>
    <p><strong>Porcentagem de Acertos:</strong> <?= $porcentagem ?>%</p>

    <h2 class="text-2xl font-semibold mt-6 mb-2">Desempenho por Categoria</h2>
    <table class="min-w-full bg-white/20 rounded-xl overflow-hidden text-white mt-2">
      <thead class="bg-white/30 text-sm font-bold">
        <tr>
          <th class="py-2 px-4 text-left">Categoria</th>
          <th class="py-2 px-4 text-center">Respondidas</th>
          <th class="py-2 px-4 text-center">Acertos</th>
          <th class="py-2 px-4 text-center">Porcentagem</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($linha = $categoriaQuery->fetch_assoc()):
            $cat_total = $linha['total'];
            $cat_acertos = $linha['acertos'];
            $cat_pct = ($cat_total > 0) ? round(($cat_acertos / $cat_total) * 100) : 0;
        ?>
          <tr class="border-t border-white/20 text-sm">
            <td class="py-2 px-4"><?= $linha['categoria'] ?></td>
            <td class="py-2 px-4 text-center"><?= $cat_total ?></td>
            <td class="py-2 px-4 text-center"><?= $cat_acertos ?></td>
            <td class="py-2 px-4 text-center"><?= $cat_pct ?>%</td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="mt-6 text-center">
      <a href="listar_jogadores.php" class="text-yellow-300 underline hover:text-yellow-200">← Voltar à Lista</a>
    </div>
  </div>

</body>
</html>
