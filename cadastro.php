<?php
include 'conexao.php';

// Verifica se os campos vieram
$nome  = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = trim($_POST['senha']);
$captcha = $_POST['g-recaptcha-response'];

if (empty($nome) || empty($email) || empty($senha) || empty($captcha)) {
    echo "<script>alert('Preencha todos os campos.'); window.location.href='cadastro.html';</script>";
    exit();
}

// Validação do CAPTCHA
$secret = '6Lc49GgrAAAAADS4ReGHk27RvCiCIKGKrbHTx7QZ'; // sua chave secreta
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");
$captchaResult = json_decode($response);

if (!$captchaResult->success) {
    echo "<script>alert('Erro na verificação do reCAPTCHA.'); window.location.href='cadastro.html';</script>";
    exit();
}

// Validação de email e senha
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('E-mail inválido.'); window.location.href='cadastro.html';</script>";
    exit();
}

if (strlen($senha) < 5 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/\d/', $senha)) {
    echo "<script>alert('A senha deve conter pelo menos 5 caracteres, incluindo letras e números.'); window.location.href='cadastro.html';</script>";
    exit();
}

// Verifica se e-mail já está cadastrado
$sql = "SELECT id FROM jogadores WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Este e-mail já está cadastrado.'); window.location.href='cadastro.html';</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// Cria hash e código de ativação
$hash = password_hash($senha, PASSWORD_DEFAULT);
$codigo = bin2hex(random_bytes(16)); // código de ativação único

$sql = "INSERT INTO jogadores (nome, email, senha, tipo, ativo, codigo_ativacao) VALUES (?, ?, ?, 'aluno', 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $hash, $codigo);

if ($stmt->execute()) {
    // Envia e-mail de ativação
    include 'enviar_ativacao.php';
    enviarLinkAtivacao($email, $nome, $codigo);

    echo "<script>alert('Cadastro realizado! Verifique seu e-mail para ativar a conta.'); window.location.href='login.html';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar.'); window.location.href='cadastro.html';</script>";
}

$stmt->close();
$conn->close();
?>
