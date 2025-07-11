<?php

namespace Source\App\Controllers;

use League\Plates\Engine;
use Source\App\Models\Contato;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $mensagem = '';
        $tipo_mensagem = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $assunto = trim($_POST['assunto'] ?? '');
            $msg = trim($_POST['mensagem'] ?? '');

            if (!$nome || !$email || !$msg || !$assunto) {
                $mensagem = "Todos os campos são obrigatórios.";
                $tipo_mensagem = "danger";
            } else {
                $salvo = Contato::salvar([
                    "nome"     => $nome,
                    "email"    => $email,
                    "mensagem" => $msg
                ]);

                if ($salvo) {
                    // ✅ Enviar e-mail
                    $enviado = $this->enviarEmail($nome, $email, $assunto, $msg);

                    if ($enviado) {
                        $_SESSION['messageSuccess'] = "Contato enviado com sucesso!";
                    } else {
                        $_SESSION['messageSuccess'] = "Contato salvo, mas falha ao enviar e-mail.";
                    }

                    header("Location: " . url("admin/contato"));
                    exit;
                } else {
                    $mensagem = "Erro ao salvar o contato. Tente novamente.";
                    $tipo_mensagem = "danger";
                }
            }
        }

        // Mensagem pós-redirecionamento
        if (isset($_SESSION['messageSuccess'])) {
            $mensagem = $_SESSION['messageSuccess'];
            $tipo_mensagem = "success";
            unset($_SESSION['messageSuccess']);
        }

        echo $this->view->render("contato/index", [
            "mensagem" => $mensagem,
            "tipo_mensagem" => $tipo_mensagem
        ]);
    }

    /**
     * Envia o e-mail de contato com PHPMailer
     */
    private function enviarEmail(string $nome, string $email, string $assunto, string $mensagem): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Ativar debug (opcional para testes)
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            // Configurações do servidor Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'franciscos.oliveira.filho@gmail.com'; // ✅ Seu e-mail
            $mail->Password   = 'wdol lqeb rygx qfqn'; // ✅ Senha de app (não a senha normal)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Remetente e destinatário
            $mail->setFrom($email, $nome); // Quem enviou (usuário que preencheu o formulário)
            $mail->addAddress('franciscos.oliveira.filho@gmail.com', 'Contato Site'); // ✅ Para onde será enviado

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = "
            <strong>Nome:</strong> {$nome}<br>
            <strong>Email:</strong> {$email}<br>
            <strong>Mensagem:</strong><br>
            <p>{$mensagem}</p>
        ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
            return false;
        }
    }
}
