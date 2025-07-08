<?php
$host = 'localhost';        // Servidor MySQL (padrão no XAMPP)
$usuario = 'root';          // Usuário padrão do XAMPP
$senha = '';                // Senha padrão (vazia)
$banco = 'show_milhao_db';  // Nome do seu banco (mude se necessário)

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
