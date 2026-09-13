<?php
// helper para envio e registro de notificacoes de e-mail aos tecnicos

namespace App\Core;

//========================================================
//= Servico de envio de e-mails
//========================================================

class Mailer
{

    // envia notificacao de finalizacao de servico para o tecnico responsavel
    public static function sendServiceFinishedEmail(string $toEmail, string $userName, string $description, float $price, float $commission): bool
    {
        $subject = "JM Informatica - Servico Finalizado #{$description}";

        $message = "Ola {$userName},\n\n";
        $message .= "Seu servico foi finalizado com sucesso no sistema.\n\n";
        $message .= "Detalhes do Servico:\n";
        $message .= "- Descricao: {$description}\n";
        $message .= "- Valor do Servico: R$ " . number_format($price, 2, ',', '.') . "\n";
        $message .= "- Valor da Comissao: R$ " . number_format($commission, 2, ',', '.') . "\n\n";
        $message .= "Atenciosamente,\nJM Informatica";

        $headers = "From: nao-responda@jminformatica.com.br\r\n" .
            "Reply-To: contato@jminformatica.com.br\r\n" .
            "X-Mailer: PHP/" . phpversion();

        // registra em log local para garantia de rastreabilidade
        $logDir = ROOT_PATH . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logContent = "[" . date('Y-m-d H:i:s') . "] Para: {$toEmail} | Assunto: {$subject} | Comissao: R$ {$commission}\n";
        file_put_contents($logDir . '/email.log', $logContent, FILE_APPEND);

        // tenta disparar via funcao nativa mail do php
        @mail($toEmail, $subject, $message, $headers);

        return true;
    }
}
