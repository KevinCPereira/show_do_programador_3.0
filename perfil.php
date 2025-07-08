<?php
session_start();
if (!isset($_SESSION['LOGGED_IN'])) {
    header("Location: login.html");
    exit();
}
include 'verifica_sessao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

include 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$nome = $_SESSION['usuario_nome'];

// Total respondido
$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM respostas WHERE usuario_id = $usuario_id");
$total = $totalQuery->fetch_assoc()['total'];

// Total de acertos
$acertosQuery = $conn->query("SELECT COUNT(*) AS acertos FROM respostas WHERE usuario_id = $usuario_id AND acertou = 1");
$acertos = $acertosQuery->fetch_assoc()['acertos'];

// Porcentagem
$porcentagem = ($total > 0) ? round(($acertos / $total) * 100) : 0;

// Desempenho por categoria
$categoriaQuery = $conn->query("
  SELECT c.nome AS categoria, COUNT(*) AS total, 
         SUM(r.acertou) AS acertos
  FROM respostas r
  JOIN perguntas p ON r.pergunta_id = p.id
  JOIN categorias c ON p.categoria_id = c.id
  WHERE r.usuario_id = $usuario_id
  GROUP BY c.nome
");

// Gerar dados para o gráfico
$categorias = [];
$acertosPorCategoria = [];

$categoriaQuery->data_seek(0);
while ($linha = $categoriaQuery->fetch_assoc()) {
    $categorias[] = $linha['categoria'];
    $acertosPorCategoria[] = $linha['acertos'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Perfil do Jogador</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white min-h-screen flex flex-col items-center justify-center p-6">

  <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl shadow-2xl w-full max-w-3xl text-center">
    <div class="flex justify-end items-center space-x-4 mb-6">
      <div class="w-10 h-10 rounded-full bg-yellow-300 flex items-center justify-center text-black font-bold">
        <?= strtoupper(substr($nome, 0, 1)) ?>
      </div>
      <span class="text-white/80 font-medium"><?= $nome ?></span>
      <a href="logout.php" class="text-red-400 hover:text-red-300 font-semibold underline">Sair</a>
    </div>

    <h1 class="text-3xl font-bold mb-4">👤 Perfil do Jogador</h1>

    <div class="text-left mb-6">
      <p><strong>Total de Perguntas Respondidas:</strong> <?= $total ?></p>
      <p><strong>Total de Acertos:</strong> <?= $acertos ?></p>
      <p><strong>Porcentagem de Acertos:</strong> <?= $porcentagem ?>%</p>
    </div>

    <h2 class="text-2xl font-semibold mb-4">📊 Desempenho por Categoria</h2>
    <div class="overflow-x-auto mb-8">
      <table class="min-w-full bg-white/20 rounded-xl overflow-hidden text-white">
        <thead class="bg-white/30 text-sm font-bold">
          <tr>
            <th class="py-2 px-4 text-left">Categoria</th>
            <th class="py-2 px-4 text-center">Respondidas</th>
            <th class="py-2 px-4 text-center">Acertos</th>
            <th class="py-2 px-4 text-center">Porcentagem</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $categoriaQuery->data_seek(0);
          while ($linha = $categoriaQuery->fetch_assoc()):
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
    </div>

    <h2 class="text-2xl font-semibold mb-4">📈 Gráfico de Acertos por Categoria</h2>
    <div class="mb-6">
      <canvas id="graficoCategoria" class="bg-white/10 rounded-xl p-4"></canvas>
    </div>

    <div class="mt-6">
      <a href="telaInicial.php" class="text-yellow-300 underline hover:text-yellow-200">← Voltar para o início</a>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('graficoCategoria').getContext('2d');

    const grafico = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($categorias) ?>,
        datasets: [{
          label: 'Acertos por Categoria',
          data: <?= json_encode($acertosPorCategoria) ?>,
          backgroundColor: 'rgba(34, 211, 238, 0.7)', // ciano vibrante
          borderColor: 'rgba(34, 211, 238, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              color: '#fff'
            }
          },
          x: {
            ticks: {
              color: '#fff'
            }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1f2937',
            titleColor: '#fff',
            bodyColor: '#fff'
          }
        }
      }
    });
  </script>
</body>
</html>
