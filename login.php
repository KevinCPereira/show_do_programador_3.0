<?php
session_start();
include 'conexao.php';
include 'auditar.php';
include_once 'notificar.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT id, nome, senha, tipo, email, ativo, tentativas_falhas, bloqueado_ate FROM jogadores WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            $usuario_id = $usuario['id'];

            // Verifica se conta está ativada
            if (!$usuario['ativo']) {
                registrarAcao($conn, null, 'Login falhou', "Conta inativa - E-mail: $email");

                $mensagem = "<p>Foi feita uma tentativa de login em uma conta ainda não ativada neste e-mail <strong>$email</strong> em <strong>" . date('d/m/Y H:i') . "</strong>.</p>";
                enviarNotificacao($email, 'Tentativa de login em conta inativa', $mensagem);

                echo "<script>alert('Conta ainda não ativada. Verifique seu e-mail.'); window.location.href='login.html';</script>";
                exit();
            }

            // Verifica bloqueio
            if ($usuario['bloqueado_ate'] && strtotime($usuario['bloqueado_ate']) > time()) {
                $tempo = date("H:i:s", strtotime($usuario['bloqueado_ate']));
                echo "<script>alert('Conta temporariamente bloqueada até $tempo devido a muitas tentativas falhas.'); window.location.href='login.html';</script>";
                exit();
            }

            // Verifica senha
            if (password_verify($senha, $usuario['senha'])) {
                // Sucesso: limpa tentativas e bloqueio
                $conn->query("UPDATE jogadores SET tentativas_falhas = 0, bloqueado_ate = NULL WHERE id = $usuario_id");

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                $_SESSION['usuario_email'] = $usuario['email'];

                registrarAcao($conn, $usuario['id'], 'Login', 'Usuário realizou login com sucesso.');

                $mensagem = "<p>Olá <strong>{$usuario['nome']}</strong>,</p>
                             <p>Um login foi realizado com sucesso em sua conta no dia <strong>" . date("d/m/Y H:i") . "</strong>.</p>";
                enviarNotificacao($usuario['email'], 'Login realizado com sucesso', $mensagem);

                header("Location: enviar_codigo.php");
                exit();
            } else {
                // Senha incorreta
                $novaTentativa = $usuario['tentativas_falhas'] + 1;
                $bloqueio = ($novaTentativa >= 5) ? ", bloqueado_ate = DATE_ADD(NOW(), INTERVAL 15 MINUTE)" : "";

                $conn->query("UPDATE jogadores SET tentativas_falhas = $novaTentativa $bloqueio WHERE id = $usuario_id");

                registrarAcao($conn, null, 'Login falhou', "E-mail: $email - Senha incorreta");

                $mensagem = "<p>Foi feita uma tentativa de login usando o e-mail <strong>$email</strong> com senha incorreta em <strong>" . date('d/m/Y H:i') . "</strong>.</p>";
                enviarNotificacao($email, 'Tentativa de login com senha incorreta', $mensagem);

                if ($novaTentativa >= 5) {
                    echo "<script>alert('Conta temporariamente bloqueada após 5 tentativas falhas. Tente novamente mais tarde.'); window.location.href='login.html';</script>";
                    exit();
                }
            }
        } else {
            // Conta não encontrada
            registrarAcao($conn, null, 'Login falhou', "E-mail: $email - Conta não encontrada");

            $mensagem = "<p>Uma tentativa de login foi feita usando o e-mail <strong>$email</strong> em <strong>" . date('d/m/Y H:i') . "</strong>, mas nenhuma conta foi encontrada.</p>";
            enviarNotificacao($email, 'Tentativa de login em conta inexistente', $mensagem);
        }

        echo "<script>alert('E-mail ou senha inválidos!'); window.location.href='login.html';</script>";
        exit();
    } else {
        echo "<script>alert('Preencha todos os campos!'); window.location.href='login.html';</script>";
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
