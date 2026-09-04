<?php
declare(strict_types=1);

// EdusIA Painel Comercial — API PHP/MySQL para Hostinger (hospedagem compartilhada)
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

// --- autenticação simples por token de equipe ---
$sentToken = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
if (!hash_equals((string)$config['api_token'], (string)$sentToken)) {
    fail(401, 'Token inválido. Verifique o token configurado no painel.');
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

switch ($action) {

    case 'list': {
        $contacts = $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetchAll();
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

    default:
        fail(400, 'Ação desconhecida: ' . $action);
}
