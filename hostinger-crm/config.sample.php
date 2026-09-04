<?php
// Copie este arquivo para "config.php" (mesma pasta) e preencha com os dados
// reais do seu banco, encontrados em hPanel > Bancos de dados MySQL.
// NUNCA suba o config.php real para um repositório público.

return [
    // hPanel > Bancos de dados MySQL costuma usar "localhost" como host.
    'db_host' => 'localhost',
    'db_name' => 'u000000000_edusia_crm',
    'db_user' => 'u000000000_edusia',
    'db_pass' => 'TROQUE_ESTA_SENHA',

    // Token simples para proteger a API. Invente uma string longa e
    // aleatória e use o MESMO valor na tela de login do painel (index.html).
    // Isso não é uma autenticação de usuários individuais — é uma senha
    // única de equipe para impedir que estranhos leiam/gravem no banco.
    'api_token' => 'TROQUE_POR_UM_TOKEN_LONGO_E_ALEATORIO',
];
