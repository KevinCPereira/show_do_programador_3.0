<?php
include 'conexao.php';

$sql = "SELECT * FROM jogadores";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($linha = $result->fetch_assoc()) {
        echo "Nome: " . $linha['nome'] . "<br>";
    }
} else {
    echo "Nenhum jogador encontrado.";
}
?>
