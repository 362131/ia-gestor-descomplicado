<?php
// Copie este arquivo para "config.php" (mesma pasta) e preencha com os dados
// reais do seu banco, encontrados em hPanel > Bancos de dados MySQL.
// NUNCA suba o config.php real para um repositório público.

return [
    // hPanel > Bancos de dados MySQL costuma usar "localhost" como host.
    'db_host' => 'localhost',
    'db_name' => 'u000000000_{{SLUG}}_crm',
    'db_user' => 'u000000000_{{SLUG}}',
    'db_pass' => 'TROQUE_ESTA_SENHA',

    // Token simples para proteger a API — funciona como uma "senha da sala"
    // que toda a equipe digita para entrar no painel. O valor abaixo já é
    // seguro e pode ser usado como está; troque por outra frase longa sem
    // espaços se preferir escolher a sua própria.
    'api_token' => 'NXUqLwqxjJdOmgfRCMJby5mTrOPl_CfD',

    // Usados pelo cron_followup.php para enviar os e-mails automáticos de
    // follow-up (veja README.md, seção "E-mails automáticos de follow-up").
    'email_from' => 'crm@seudominio.com.br',
    'email_from_name' => '{{EMPRESA}} — Painel Comercial',
    'painel_url' => 'https://seudominio.com.br/crm/',
];
