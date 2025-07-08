# show_do_programador_3.0

# Show do Programador - Computação (README)

## 🌍 Visão Geral

Este é um projeto web educacional inspirado no formato do programa "Show do Milhão". Seu objetivo é reforçar conhecimentos sobre computação de forma gamificada e interativa. O sistema permite que jogadores respondam perguntas de múltipla escolha, acumulem pontuações e acompanhem seus desempenhos.

## 🔧 Tecnologias Utilizadas

* **Frontend:** HTML5, CSS3 (Tailwind CSS), JavaScript
* **Backend:** PHP 8+
* **Banco de Dados:** MySQL
* **Servidor Local:** XAMPP 
* **Segurança:**

  * Autenticação de dois fatores (2FA)
  * Hash de senhas com `password_hash`
  * Auditoria de login/logout
  * Validações de entrada e reCAPTCHA
  * Proteção contra tempo de inatividade
* **Email:** PHPMailer para envio de códigos e recuperação de senha

## 🌐 Funcionalidades

* Cadastro com validação e ativação via email
* Login com 2FA (código enviado por email)
* Diferenciação de perfis (aluno e professor)
* Perfil com estatísticas e gráfico por categoria
* Listagem de jogadores (visível apenas para professores)
* Auditoria de login/logout
* Proteção de rotas e controle de sessão
* Redefinição de senha via e-mail com link

## 🗄️ Banco de Dados

Este projeto utiliza um banco de dados MySQL. O arquivo de exportação (`show_milhao.db`) contém:

- Estrutura completa de tabelas
- Dados de exemplo nas tabelas:
  - `categorias`
  - `perguntas`
  - `alternativas`
  - `respostas` (para simulação)

**Importante:** Tabelas sensíveis como `jogadores`, `auditoria`, `resultados`,`respostas`, ou dados pessoais foram omitidos para preservar a segurança e privacidade,pois,essas tabelas estão ligadas com o id do usuario e para testar o programa será necessário criar sua conta de aluno pelo proprio programa e conta de professor pelo banco de dados.

---

### Como criar uma conta de professor manualmente:

Abra o **phpMyAdmin** ou o terminal MySQL e execute o seguinte comando:

```sql
INSERT INTO jogadores (nome, email, senha, tipo, ativo)
VALUES (
  'Professor Admin',
  'professor@email.com',
  '<SENHA_CRIPTOGRAFADA>',
  'professor',
  1
); 

Para gerar uma senha criptografada, você pode usar este comando no terminal do PHP:
<?php echo password_hash('123456', PASSWORD_DEFAULT); ?>
Substitua <SENHA_CRIPTOGRAFADA> pelo resultado gerado.

## 📧 Envio de E-mails (2FA e recuperação de senha)

O envio de e-mails utiliza a biblioteca PHPMailer. Para funcionar localmente, você deve:

1. Ativar a autenticação em dois fatores na sua conta Google.
2. Criar uma senha de aplicativo.
3. Inserir seu e-mail e a senha no arquivo `enviar_codigo.php`:

php
$mail->Username = 'SEU_EMAIL@gmail.com';
$mail->Password = 'SENHA_DO_APLICATIVO';

## COMO ABRIR O PROJETO
Apos fazer todas as modificações necessárias no codigo para funcionar tudo perfeitamente voce deve abrir ele pela url por este link:https://localhost/show_do_programador_3.0/TelaInicial.php

provavelmente ele vai dizer que não é seguro na URL, porém com esse video: https://www.youtube.com/watch?v=NlPvjr64yLE eu consegui criar um certificado ssl que faz desaparecer a parte denão estar seguro apontado pelo navegador usado.





