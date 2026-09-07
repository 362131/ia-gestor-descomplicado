<?php
declare(strict_types=1);

// EdusIA Painel Comercial — envio automático de e-mails de follow-up.
// Rode este arquivo uma vez por dia via Cron Job (hPanel > Avançado > Cron
// Jobs), apontando para o caminho completo deste arquivo com o comando:
//   php /home/SEU_USUARIO/public_html/crm/cron_followup.php
// Não é acessível pelo navegador com resultado útil — é feito para rodar
// só via linha de comando do cron.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Este script só pode ser executado via linha de comando (cron).";
    exit;
}

$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    fwrite(STDERR, "config.php não encontrado.\n");
    exit(1);
}
$config = require $configFile;

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Falha ao conectar ao banco de dados: " . $e->getMessage() . "\n");
    exit(1);
}

$fromEmail = $config['email_from'] ?? 'nao-responda@localhost';
$fromName = $config['email_from_name'] ?? 'CRM';
$painelUrl = $config['painel_url'] ?? '';

function safeHeaderValue(string $v): string {
    // Remove quebras de linha para impedir injeção de cabeçalhos no e-mail.
    return trim(preg_replace('/[\r\n]+/', ' ', $v));
}

function enviarEmail(string $para, string $assunto, string $corpo, string $fromEmail, string $fromName): bool {
    if (!filter_var($para, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $assunto = safeHeaderValue($assunto);
    $headers = "From: " . safeHeaderValue($fromName) . " <" . safeHeaderValue($fromEmail) . ">\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return mail($para, $assunto, $corpo, $headers);
}

// Busca interações cujo follow-up é hoje, ainda não notificadas, e cujo
// negócio não está fechado (ganho ou perdido não precisam de lembrete).
$stmt = $pdo->prepare("
    SELECT i.id, i.proxima_acao, i.data_followup, i.pilar, i.produto, i.estagio,
           c.nome AS lead_nome, c.email AS lead_email, c.empresa, c.vendedor
    FROM interactions i
    JOIN contacts c ON c.id = i.contact_id
    WHERE i.data_followup = CURDATE()
      AND i.followup_email_sent_at IS NULL
      AND i.estagio NOT IN ('ganho', 'perdido')
");
$stmt->execute();
$rows = $stmt->fetchAll();

if (!$rows) {
    echo "Nenhum follow-up para hoje.\n";
    exit(0);
}

$usersStmt = $pdo->prepare('SELECT email FROM users WHERE nome = ? LIMIT 1');

$enviados = 0;
foreach ($rows as $row) {
    $proximaAcao = $row['proxima_acao'] !== '' ? $row['proxima_acao'] : 'um follow-up agendado';

    // 1) E-mail para o lead, só se ele tiver um e-mail cadastrado.
    if (!empty($row['lead_email'])) {
        $assunto = "Follow-up: " . $proximaAcao;
        $corpo = "Olá, {$row['lead_nome']}!\n\n"
            . "Passando para lembrar sobre: {$proximaAcao}\n\n"
            . "Qualquer dúvida, é só responder este e-mail.\n\n"
            . "Atenciosamente,\nEquipe {$fromName}";
        enviarEmail($row['lead_email'], $assunto, $corpo, $fromEmail, $fromName);
    }

    // 2) Alerta interno para o vendedor responsável, se tiver conta cadastrada.
    $usersStmt->execute([$row['vendedor']]);
    $vendedorEmail = $usersStmt->fetchColumn();
    if ($vendedorEmail) {
        $assunto = "[CRM] Follow-up hoje: " . $row['lead_nome'];
        $corpo = "Você tem um follow-up agendado para hoje:\n\n"
            . "Lead: {$row['lead_nome']} ({$row['empresa']})\n"
            . "Próxima ação: {$proximaAcao}\n"
            . "Pilar: {$row['pilar']} | Produto: {$row['produto']}\n"
            . ($painelUrl ? "\nAcesse o painel: {$painelUrl}\n" : '');
        enviarEmail($vendedorEmail, $assunto, $corpo, $fromEmail, $fromName);
    }

    $pdo->prepare('UPDATE interactions SET followup_email_sent_at = NOW() WHERE id = ?')
        ->execute([$row['id']]);
    $enviados++;
}

echo "Follow-ups processados: {$enviados}\n";
