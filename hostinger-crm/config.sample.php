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

    // Token simples para proteger a API — funciona como uma "senha da sala"
    // que toda a equipe digita para entrar no painel. O valor abaixo já é
    // seguro e pode ser usado como está; troque por outra frase longa sem
    // espaços se preferir escolher a sua própria.
    'api_token' => 'NXUqLwqxjJdOmgfRCMJby5mTrOPl_CfD',
];
