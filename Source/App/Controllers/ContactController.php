<?php

namespace Source\App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ContactController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome = htmlspecialchars($_POST['nome'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $assunto = htmlspecialchars($_POST['assunto'] ?? '');
            $mensagem = htmlspecialchars($_POST['mensagem'] ?? '');

            // Validação básica
            if (empty($nome) || empty($email) || empty($assunto) || empty($mensagem)) {
                $mensagem_status = '<div class="alert-danger">Por favor, preencha todos os campos.</div>';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mensagem_status = '<div class="alert-danger">Por favor, insira um email válido.</div>';
            } else {
                try {
                    // Carrega o autoload do Composer
                    require 'vendor/autoload.php'; // Se usou Composer

                    // Ou inclua manualmente os arquivos (se fez download manual):
                    // require 'PHPMailer/src/Exception.php';
                    // require 'PHPMailer/src/PHPMailer.php';
                    // require 'PHPMailer/src/SMTP.php';

                    $mail = new PHPMailer(true);

                    // Ativar debug
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Ativa saída de debug detalhada
                    // Capturar a saída em vez de imprimir
                    ob_start();

                    // Configurações do servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Ex: smtp.gmail.com, smtp.office365.com
                    $mail->SMTPAuth = true;

                    // IMPORTANTE: Altere para o seu email
                    $mail->Username = 'franciscos.oliveira.filho@gmail.com'; // Seu email completo
                    $mail->Password = 'wdol lqeb rygx qfqn'; // Senha do email ou senha de app

                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL/TLS
                    $mail->Port = 465; // Porta do SMTP (465 para SSL, 587 para TLS)
                    $mail->CharSet = 'UTF-8'; // Garante que acentos e caracteres especiais sejam exibidos corretamente

                    // Remetente e destinatário
                    // IMPORTANTE: Altere para o seu domínio e email
                    $mail->setFrom($mail->Username, 'Sistema de Contato - Tabela de Preços');
                    $mail->addAddress($mail->Username, 'Atendimento'); // Altera para o email que recebe as mensagens
                    $mail->addReplyTo($email, $nome);

                    // Conteúdo do email
                    $mail->isHTML(true);
                    $mail->Subject = "Contato via Site: $assunto";
                    $mail->Body = "
                <h2>Nova mensagem de contato</h2>
                <p><strong>Nome:</strong> $nome</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Assunto:</strong> $assunto</p>
                <p><strong>Mensagem:</strong></p>
                <p>$mensagem</p>
            ";
                    $mail->AltBody = "Nome: $nome\nEmail: $email\nAssunto: $assunto\nMensagem:\n$mensagem";

                    $mail->send();
                    $debug_output = ob_get_clean(); // Captura a saída de debug

                    // Registrar informações de debug em um arquivo de log
                    file_put_contents('email_log.txt', date('[Y-m-d H:i:s] ') . "Email enviado para: {$mail->Username}\n" . $debug_output . "\n\n", FILE_APPEND);

                    $mensagem_status = '<div class="alert-success">Mensagem enviada com sucesso! Entraremos em contato em breve.</div>';

                    // Limpar os dados do formulário após o envio bem-sucedido
                    $_POST = array();
                } catch (\Exception $e) {
                    $debug_output = ob_get_clean(); // Captura a saída de debug em caso de erro

                    // Registrar erro em um arquivo de log
                    file_put_contents('email_error_log.txt', date('[Y-m-d H:i:s] ') . "Erro ao enviar email: {$mail->ErrorInfo}\n" . $debug_output . "\n\n", FILE_APPEND);

                    $mensagem_status = '<div class="alert-danger">Erro ao enviar mensagem. Por favor, tente novamente mais tarde ou entre em contato via WhatsApp.</div>';

                    // Mostra detalhes do erro apenas em ambiente de desenvolvimento
                    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') {
                        $mensagem_status .= '<div class="alert-danger"><strong>Detalhes do erro (apenas dev):</strong> ' . $mail->ErrorInfo . '</div>';
                    }
                }


                echo $this->view->render("contato/index", [
                    "mensagem_status" => $mensagem_status
                ]);
            }
        }
    }
}
