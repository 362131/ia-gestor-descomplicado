# Painel Comercial EdusIA — versão Hostinger (PHP + MySQL)

Esta pasta contém a versão do CRM que roda **inteiramente no seu Hostinger**,
com banco de dados MySQL próprio (não depende da plataforma Claude).

## Arquivos

- `index.html` — a página do painel (frontend).
- `api.php` — a API que lê/grava no banco MySQL.
- `config.sample.php` — modelo de configuração (credenciais do banco + token de acesso).
- `schema.sql` — script para criar as tabelas `contacts` e `interactions`.

## Passo a passo no hPanel (Hostinger)

### 1. Criar o banco de dados MySQL
1. Entre no **hPanel** > **Bancos de dados** > **Bancos de dados MySQL**.
2. Crie um novo banco (anote o nome, ex.: `u123456789_edusia_crm`).
3. Crie um usuário MySQL e uma senha forte, e associe-o ao banco com todas as permissões.
4. Anote: **host** (geralmente `localhost`), **nome do banco**, **usuário** e **senha**.

### 2. Importar a estrutura das tabelas
1. Ainda em Bancos de dados, clique em **phpMyAdmin** ao lado do banco criado.
2. Vá em **Importar**, selecione o arquivo `schema.sql` desta pasta e execute.
3. Confirme que as tabelas `contacts` e `interactions` foram criadas.

### 3. Configurar o backend
1. Copie `config.sample.php` para um novo arquivo chamado `config.php` (na mesma pasta).
2. Preencha `db_host`, `db_name`, `db_user`, `db_pass` com os dados do passo 1.
3. Defina `api_token` com uma senha longa e aleatória — é o token que a equipe vai
   usar para entrar no painel (ex.: gere uma string com um gerenciador de senhas).
4. **Nunca** deixe `config.php` acessível publicamente em um repositório Git.

### 4. Subir os arquivos
1. No hPanel, vá em **Arquivos** > **Gerenciador de Arquivos** (ou use FTP).
2. Crie uma subpasta em `public_html` (ex.: `public_html/crm`) ou use um subdomínio
   próprio (ex.: `crm.seudominio.com`) apontando para essa pasta — recomendado para
   isolar o painel do restante do site.
3. Envie `index.html`, `api.php` e o seu `config.php` (com as credenciais reais)
   para essa pasta. **Não** suba `config.sample.php` nem `schema.sql` para o servidor
   se preferir manter a pasta enxuta (não é obrigatório removê-los, mas não são necessários lá).

### 5. Acessar o painel
1. Abra `https://seudominio.com/crm/` (ou o subdomínio escolhido).
2. Digite o token de acesso definido em `config.php`.
3. Pronto — o painel já lê e grava direto no seu banco MySQL do Hostinger.

## Segurança — o que este modelo cobre e o que não cobre

- **Cobre:** protege a API com um token compartilhado (só quem tem o token acessa
  os dados) e usa HTTPS automaticamente se seu domínio no Hostinger tiver SSL
  ativado (ative gratuitamente em hPanel > SSL).
- **Não cobre:** login individual por vendedor (usuário/senha por pessoa),
  histórico de "quem alterou o quê", ou recuperação de senha — é um token único
  de equipe, adequado para um time pequeno e confiável. Se no futuro for
  necessário controle de acesso por pessoa, isso exigiria adicionar uma tabela
  de usuários e autenticação própria (posso construir isso depois, se quiser).
- Troque o `api_token` periodicamente se alguém sair da equipe.

## Backup

Use o **phpMyAdmin** (Exportar) para gerar um backup `.sql` do banco periodicamente,
ou use o botão **Exportar CSV** dentro do próprio painel para uma cópia rápida em
planilha.

## Diferenças em relação à versão "Artifact" (Claude)

- Antes: dados ficavam no armazenamento interno da plataforma Claude.
- Agora: dados ficam no MySQL do seu Hostinger — você tem controle total,
  pode fazer backup, migrar, ou consultar via phpMyAdmin diretamente.
- A funcionalidade do painel (cadastro, histórico, DISC, KPIs, filtros, CSV)
  é idêntica nas duas versões.
