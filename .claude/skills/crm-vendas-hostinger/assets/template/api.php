<?php
declare(strict_types=1);

// {{EMPRESA}} Painel Comercial — API PHP/MySQL para Hostinger (hospedagem compartilhada)
// Endpoint único: api.php?action=...
// Todas as respostas são JSON. Erros retornam {"error": "..."} com status HTTP != 200.

header('Content-Type: application/json; charset=utf-8');

$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'config.php não encontrado. Copie config.sample.php para config.php e preencha os dados do banco.']);
    exit;
}
$config = require $configFile;

function fail(int $status, string $msg): void {
    http_response_code($status);
    echo json_encode(['error' => $msg]);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    fail(500, 'Falha ao conectar ao banco de dados: ' . $e->getMessage());
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];

function uid(): string {
    return bin2hex(random_bytes(8));
}

function reqStr($v, string $field, bool $required = true): string {
    $s = is_string($v) ? trim($v) : '';
    if ($required && $s === '') fail(422, "Campo obrigatório ausente: $field");
    return $s;
}

// --- login: não exige token, verifica e-mail + senha ---
if ($action === 'login') {
    $email = strtolower(reqStr($input['email'] ?? null, 'email'));
    $senha = reqStr($input['senha'] ?? null, 'senha');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($senha, $user['password_hash'])) {
        fail(401, 'E-mail ou senha inválidos.');
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $pdo->prepare('INSERT INTO access_log (user_id, nome, email, ip, ocorrido_em) VALUES (?, ?, ?, ?, ?)')
        ->execute([$user['id'], $user['nome'], $user['email'], $ip, date('Y-m-d H:i:s')]);

    echo json_encode([
        'token' => $user['api_token'],
        'nome' => $user['nome'],
        'isAdmin' => (bool)$user['is_admin'],
    ]);
    exit;
}

// --- autenticação: token mestre (config.php) OU token de um usuário cadastrado ---
$sentToken = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
$currentUser = null; // null quando autenticado pelo token mestre

if (hash_equals((string)$config['api_token'], (string)$sentToken)) {
    $currentUser = ['id' => null, 'nome' => 'Administrador', 'is_admin' => 1];
} else {
    $stmt = $pdo->prepare('SELECT id, nome, is_admin FROM users WHERE api_token = ?');
    $stmt->execute([$sentToken]);
    $currentUser = $stmt->fetch() ?: null;
}

if (!$currentUser) {
    fail(401, 'Sessão inválida. Faça login novamente.');
}

function requireAdmin($currentUser): void {
    if (empty($currentUser['is_admin'])) {
        fail(403, 'Apenas administradores podem fazer isso.');
    }
}

$isAdmin = !empty($currentUser['is_admin']);
$meuNome = $currentUser['nome'] ?? '';

switch ($action) {

    case 'list': {
        // Acesso total (todos os contatos) é só para administradores; cada
        // vendedor só recebe os próprios leads (contacts.vendedor = seu nome).
        if ($isAdmin) {
            $contacts = $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetchAll();
        } else {
            $stmt = $pdo->prepare('SELECT * FROM contacts WHERE vendedor = ? ORDER BY created_at DESC');
            $stmt->execute([$meuNome]);
            $contacts = $stmt->fetchAll();
        }
        $interactions = $pdo->query('SELECT * FROM interactions ORDER BY data_hora DESC')->fetchAll();

        $byContact = [];
        foreach ($interactions as $it) {
            $byContact[$it['contact_id']][] = [
                'id' => $it['id'],
                'dataHora' => str_replace(' ', 'T', $it['data_hora']),
                'canal' => $it['canal'],
                'resumo' => $it['resumo'],
                'temperatura' => $it['temperatura'],
                'proximaAcao' => $it['proxima_acao'],
                'dataFollowUp' => $it['data_followup'],
                'pilar' => $it['pilar'],
                'produto' => $it['produto'],
                'valor' => (float)$it['valor'],
                'estagio' => $it['estagio'],
                'vendedorRegistro' => $it['vendedor_registro'],
                'createdAt' => str_replace(' ', 'T', $it['created_at']),
            ];
        }

        // $byContact pode conter interações de contatos de outros vendedores
        // (a query acima busca todas), mas só as dos contatos abaixo (já
        // filtrados) chegam ao $out — nada de outros vendedores vaza.
        $out = [];
        foreach ($contacts as $c) {
            $out[] = [
                'id' => $c['id'],
                'nome' => $c['nome'],
                'empresa' => $c['empresa'],
                'cargo' => $c['cargo'],
                'telefone' => $c['telefone'],
                'email' => $c['email'],
                'origem' => $c['origem'],
                'disc' => $c['disc'],
                'vendedor' => $c['vendedor'],
                'tags' => $c['tags'] ? json_decode($c['tags'], true) : [],
                'createdAt' => str_replace(' ', 'T', $c['created_at']),
                'interactions' => $byContact[$c['id']] ?? [],
            ];
        }
        echo json_encode($out);
        break;
    }

    case 'save_contact': {
        $id = isset($input['id']) && $input['id'] !== '' ? (string)$input['id'] : uid();
        $nome = reqStr($input['nome'] ?? null, 'nome');
        $telefone = reqStr($input['telefone'] ?? null, 'telefone');
        $vendedor = reqStr($input['vendedor'] ?? null, 'vendedor');
        $origem = reqStr($input['origem'] ?? null, 'origem');

        $exists = $pdo->prepare('SELECT id, created_at FROM contacts WHERE id = ?');
        $exists->execute([$id]);
        $row = $exists->fetch();
        $createdAt = $row ? $row['created_at'] : ($input['createdAt'] ?? date('Y-m-d H:i:s'));

        $tagsJson = json_encode(array_values($input['tags'] ?? []));

        $stmt = $pdo->prepare('
            REPLACE INTO contacts (id, nome, empresa, cargo, telefone, email, origem, disc, vendedor, tags, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $id, $nome,
            reqStr($input['empresa'] ?? null, 'empresa', false),
            reqStr($input['cargo'] ?? null, 'cargo', false),
            $telefone,
            reqStr($input['email'] ?? null, 'email', false),
            $origem,
            reqStr($input['disc'] ?? null, 'disc', false),
            $vendedor,
            $tagsJson,
            $createdAt,
        ]);
        echo json_encode(['id' => $id]);
        break;
    }

    case 'delete_contact': {
        $id = reqStr($input['id'] ?? null, 'id');
        $pdo->prepare('DELETE FROM contacts WHERE id = ?')->execute([$id]);
        echo json_encode(['ok' => true]);
        break;
    }

    case 'add_interaction': {
        $contactId = reqStr($input['contactId'] ?? null, 'contactId');
        $chk = $pdo->prepare('SELECT id FROM contacts WHERE id = ?');
        $chk->execute([$contactId]);
        if (!$chk->fetch()) fail(404, 'Contato não encontrado.');

        $id = uid();
        $stmt = $pdo->prepare('
            INSERT INTO interactions
              (id, contact_id, data_hora, canal, resumo, temperatura, proxima_acao, data_followup, pilar, produto, valor, estagio, vendedor_registro, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $id, $contactId,
            str_replace('T', ' ', reqStr($input['dataHora'] ?? null, 'dataHora')),
            reqStr($input['canal'] ?? null, 'canal'),
            reqStr($input['resumo'] ?? null, 'resumo'),
            reqStr($input['temperatura'] ?? null, 'temperatura'),
            reqStr($input['proximaAcao'] ?? null, 'proximaAcao', false),
            (isset($input['dataFollowUp']) && $input['dataFollowUp'] !== '') ? $input['dataFollowUp'] : null,
            reqStr($input['pilar'] ?? null, 'pilar'),
            reqStr($input['produto'] ?? null, 'produto'),
            (float)($input['valor'] ?? 0),
            reqStr($input['estagio'] ?? null, 'estagio'),
            reqStr($input['vendedorRegistro'] ?? null, 'vendedorRegistro', false),
            date('Y-m-d H:i:s'),
        ]);
        echo json_encode(['id' => $id]);
        break;
    }

    case 'delete_interaction': {
        $id = reqStr($input['id'] ?? null, 'id');
        $pdo->prepare('DELETE FROM interactions WHERE id = ?')->execute([$id]);
        echo json_encode(['ok' => true]);
        break;
    }

    case 'ranking': {
        // Ranking geral (negócios fechados-ganho) visível para toda a
        // equipe. O valor em R$ só aparece para administradores e para o
        // próprio vendedor na sua própria linha — os demais veem só a
        // posição e a quantidade de negócios ganhos.
        $rows = $pdo->query("
            SELECT c.vendedor AS vendedor,
                   SUM(CASE WHEN i.estagio = 'ganho' THEN 1 ELSE 0 END) AS ganhos,
                   SUM(CASE WHEN i.estagio = 'ganho' THEN i.valor ELSE 0 END) AS valor
            FROM contacts c
            LEFT JOIN interactions i ON i.contact_id = c.id
            GROUP BY c.vendedor
            ORDER BY ganhos DESC, valor DESC
        ")->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $ehMinhaLinha = trim($r['vendedor']) === trim($meuNome);
            $out[] = [
                'vendedor' => $r['vendedor'],
                'ganhos' => (int)$r['ganhos'],
                'valor' => ($isAdmin || $ehMinhaLinha) ? (float)$r['valor'] : null,
                'ehMinhaLinha' => $ehMinhaLinha,
            ];
        }
        echo json_encode($out);
        break;
    }

    case 'list_sellers': {
        $rows = $pdo->query('SELECT nome FROM sellers ORDER BY nome')->fetchAll();
        echo json_encode(array_map(fn($r) => $r['nome'], $rows));
        break;
    }

    case 'add_seller': {
        $nome = reqStr($input['nome'] ?? null, 'nome');
        $pdo->prepare('INSERT IGNORE INTO sellers (nome) VALUES (?)')->execute([$nome]);
        echo json_encode(['ok' => true]);
        break;
    }

    case 'change_password': {
        if (empty($currentUser['id'])) {
            fail(400, 'Entre com seu e-mail e senha (não com o token mestre) para trocar a senha.');
        }
        $novaSenha = reqStr($input['novaSenha'] ?? null, 'novaSenha');
        if (strlen($novaSenha) < 6) {
            fail(422, 'A nova senha precisa ter pelo menos 6 caracteres.');
        }
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $currentUser['id']]);
        echo json_encode(['ok' => true]);
        break;
    }

    case 'list_users': {
        requireAdmin($currentUser);
        $rows = $pdo->query('SELECT id, nome, email, is_admin, created_at FROM users ORDER BY nome')->fetchAll();
        echo json_encode(array_map(function($r) {
            return [
                'id' => (int)$r['id'],
                'nome' => $r['nome'],
                'email' => $r['email'],
                'isAdmin' => (bool)$r['is_admin'],
                'createdAt' => str_replace(' ', 'T', $r['created_at']),
            ];
        }, $rows));
        break;
    }

    case 'add_user': {
        requireAdmin($currentUser);
        $nome = reqStr($input['nome'] ?? null, 'nome');
        $email = strtolower(reqStr($input['email'] ?? null, 'email'));
        $senha = reqStr($input['senha'] ?? null, 'senha');
        if (strlen($senha) < 6) {
            fail(422, 'A senha precisa ter pelo menos 6 caracteres.');
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(24));
        try {
            $pdo->prepare('
                INSERT INTO users (nome, email, password_hash, api_token, is_admin, created_at)
                VALUES (?, ?, ?, ?, 0, ?)
            ')->execute([$nome, $email, $hash, $token, date('Y-m-d H:i:s')]);
        } catch (PDOException $e) {
            fail(409, 'Já existe uma conta com esse e-mail.');
        }
        $pdo->prepare('INSERT IGNORE INTO sellers (nome) VALUES (?)')->execute([$nome]);
        echo json_encode(['ok' => true]);
        break;
    }

    case 'list_access_log': {
        requireAdmin($currentUser);
        $rows = $pdo->query('SELECT nome, email, ip, ocorrido_em FROM access_log ORDER BY ocorrido_em DESC LIMIT 300')->fetchAll();
        echo json_encode(array_map(function($r) {
            return [
                'nome' => $r['nome'],
                'email' => $r['email'],
                'ip' => $r['ip'],
                'ocorridoEm' => str_replace(' ', 'T', $r['ocorrido_em']),
            ];
        }, $rows));
        break;
    }

    case 'suggest_reply': {
        $contactId = reqStr($input['contactId'] ?? null, 'contactId');
        $respostaComprador = reqStr($input['respostaComprador'] ?? null, 'respostaComprador');

        $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute([$contactId]);
        $contact = $stmt->fetch();
        if (!$contact) fail(404, 'Contato não encontrado.');
        if (!$isAdmin && trim($contact['vendedor']) !== trim($meuNome)) {
            fail(403, 'Você só pode gerar sugestões para os próprios leads.');
        }

        $apiKey = $config['anthropic_api_key'] ?? '';
        if (!$apiKey) {
            fail(500, 'Chave de IA não configurada. Adicione "anthropic_api_key" no config.php.');
        }
        $model = $config['ai_model'] ?? 'claude-haiku-4-5-20251001';

        $intStmt = $pdo->prepare('SELECT * FROM interactions WHERE contact_id = ? ORDER BY data_hora DESC LIMIT 5');
        $intStmt->execute([$contactId]);
        $interactions = array_reverse($intStmt->fetchAll());

        $historico = '';
        foreach ($interactions as $it) {
            $historico .= "- [{$it['data_hora']}] Canal: {$it['canal']}. Resumo: {$it['resumo']}. "
                . "Estágio: {$it['estagio']}. Produto: {$it['produto']} ({$it['pilar']}). "
                . "Valor da proposta: R$ " . number_format((float)$it['valor'], 2, ',', '.') . ".\n";
        }
        if ($historico === '') {
            $historico = "(nenhuma conversa registrada ainda)\n";
        }

        $empresaNome = $config['email_from_name'] ?? 'a empresa';
        $contexto = "Lead: {$contact['nome']}"
            . ($contact['empresa'] ? " ({$contact['empresa']})" : '')
            . ($contact['cargo'] ? ", cargo: {$contact['cargo']}" : '')
            . ($contact['disc'] ? ". Perfil DISC: {$contact['disc']}" : '') . "\n"
            . "Origem do lead: {$contact['origem']}\n\n"
            . "Histórico de conversas recentes:\n{$historico}\n"
            . "O comprador acabou de responder o seguinte:\n\"{$respostaComprador}\"\n\n"
            . "Escreva a melhor resposta possível para convencê-lo a avançar na negociação.";

        $fraseEmpresa = $config['frase_padrao'] ?? '';
        $system = "Você é um assistente de vendas experiente ajudando um vendedor de {$empresaNome}. "
            . "Sua tarefa é sugerir uma resposta persuasiva, informal e direta (não robótica, nada de "
            . "formalidade exagerada) para o vendedor enviar ao lead, considerando o perfil DISC dele "
            . "quando disponível (D=direto e objetivo, I=entusiasmado e social, S=paciente e "
            . "tranquilizador, C=lógico e detalhado). Escreva como alguém que manda mensagem de "
            . "verdade no WhatsApp: frases curtas, sem rodeio, pode usar contrações e uma linguagem "
            . "mais solta, sem perder o profissionalismo nem parecer geração automática. Vá direto ao "
            . "ponto da objeção do comprador nas primeiras linhas, sem introdução genérica. "
            . "Responda em português do Brasil, pronto para o vendedor copiar e enviar como está "
            . "(sem aspas ao redor do texto). Não invente promessas, preços, prazos ou condições que "
            . "não estejam no histórico fornecido — se precisar de uma informação que não tem, deixe "
            . "um espaço claro tipo [confirmar prazo] em vez de inventar."
            . ($fraseEmpresa ? " Quando fizer sentido no contexto (não em toda resposta, só quando "
                . "reforçar o argumento), pode encaixar de forma natural — nunca robótica — uma "
                . "variação desta frase institucional da empresa: \"{$fraseEmpresa}\"." : '');

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
                'content-type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'max_tokens' => 700,
                'system' => $system,
                'messages' => [['role' => 'user', 'content' => $contexto]],
            ]),
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            fail(502, 'Falha ao conectar com a IA: ' . $curlError);
        }
        $decoded = json_decode($response, true);
        if ($httpCode !== 200 || !isset($decoded['content'][0]['text'])) {
            $errMsg = $decoded['error']['message'] ?? ('HTTP ' . $httpCode);
            fail(502, 'Falha ao gerar sugestão com IA: ' . $errMsg);
        }

        echo json_encode(['sugestao' => trim($decoded['content'][0]['text'])]);
        break;
    }

    case 'delete_user': {
        requireAdmin($currentUser);
        $id = reqStr($input['id'] ?? null, 'id');
        if ($currentUser['id'] !== null && (int)$id === (int)$currentUser['id']) {
            fail(400, 'Você não pode remover a própria conta.');
        }
        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
        echo json_encode(['ok' => true]);
        break;
    }

    default:
        fail(400, 'Ação desconhecida: ' . $action);
}
