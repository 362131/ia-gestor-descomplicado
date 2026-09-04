# Painel Comercial EdusIA — versão Hostinger (guia simples)

Siga esta ordem exata, sem pular passos. Leva uns 15 minutos, mesmo sem
experiência técnica.

## O que você vai fazer, em 5 passos

1. Criar um banco de dados no hPanel (só clicar em botões).
2. Colar um código pronto no phpMyAdmin (copiar e colar, uma vez).
3. Editar 1 arquivo de texto com 4 informações (copiar e colar, com atenção).
4. Enviar 3 arquivos para o Hostinger (arrastar e soltar).
5. Abrir o link e usar.

---

### Passo 1 — Criar o banco de dados

1. Entre no **hPanel** (painel do Hostinger).
2. No menu, procure **"Bancos de dados"** → **"Bancos de dados MySQL"**.
3. Em **"Criar novo banco de dados MySQL"**, digite um nome, por exemplo `edusia_crm`, e clique em criar.
   - O Hostinger vai colocar um prefixo automático, tipo `u123456789_edusia_crm`. Tudo bem, é normal.
4. Logo abaixo, crie um **usuário** para esse banco (ex.: `edusia_user`) e uma **senha**.
   - Clique em **"Gerar"** para criar uma senha forte automaticamente, e **copie e guarde essa senha** num lugar seguro (ex.: nas notas do celular).
5. Associe esse usuário ao banco que você criou, com **todos os privilégios** marcados.

📝 **Anote em algum lugar, você vai precisar logo mais:**
- Nome do banco: `______________`
- Usuário do banco: `______________`
- Senha do banco: `______________`
- Host: normalmente é `localhost` (não precisa anotar, já vem assim)

---

### Passo 2 — Criar as tabelas (copiar e colar)

1. Volte em **"Bancos de dados MySQL"** e clique no botão **"phpMyAdmin"** ao lado do banco que você criou.
2. Vai abrir uma tela nova. No menu de cima, clique em **"SQL"**.
3. Abra o arquivo `schema.sql` (está nesta mesma pasta), selecione todo o conteúdo e copie.
4. Cole na caixa de texto grande do phpMyAdmin e clique em **"Executar"** (ou "Go").
5. Deve aparecer uma mensagem verde de sucesso. Pronto, as tabelas foram criadas.

---

### Passo 3 — Configurar o arquivo `config.php`

1. Nesta pasta, faça uma cópia do arquivo `config.sample.php` e renomeie a cópia para `config.php`.
2. Abra `config.php` em qualquer editor de texto simples (Bloco de Notas serve) e troque **apenas** estas 5 linhas pelos dados que você anotou no Passo 1:

```php
'db_host' => 'localhost',
'db_name' => 'COLE_AQUI_O_NOME_DO_BANCO',
'db_user' => 'COLE_AQUI_O_USUARIO_DO_BANCO',
'db_pass' => 'COLE_AQUI_A_SENHA_DO_BANCO',
'api_token' => 'NXUqLwqxjJdOmgfRCMJby5mTrOPl_CfD',
```

> O `api_token` acima já vem pronto para você usar (é a "senha de entrada" do
> painel para toda a equipe). Pode usar como está, ou trocar por outra frase
> longa sem espaços — só lembre que é essa mesma frase que todos vão digitar
> para entrar no painel.

3. Salve o arquivo.

---

### Passo 4 — Enviar os arquivos para o Hostinger

1. No hPanel, vá em **"Arquivos"** → **"Gerenciador de Arquivos"**.
2. Entre na pasta `public_html`.
3. Crie uma pasta nova chamada `crm` (botão "Nova pasta").
4. Entre na pasta `crm` e clique em **"Enviar"** (upload). Envie estes 3 arquivos desta pasta local:
   - `index.html`
   - `api.php`
   - `config.php` (o que você acabou de editar no Passo 3 — **não** envie o `config.sample.php`)

---

### Passo 5 — Usar o painel

1. Abra no navegador: `https://SEUDOMINIO.com/crm/` (troque `SEUDOMINIO.com` pelo seu domínio real).
2. Digite o token do Passo 3 (`NXUqLwqxjJdOmgfRCMJby5mTrOPl_CfD`, se não trocou).
3. Pronto — cadastre um contato de teste para confirmar que está tudo salvando.

---

## Se algo der errado

| Mensagem/sintoma | O que fazer |
|---|---|
| "Token inválido" | Confira se digitou exatamente o mesmo texto do `api_token` em `config.php`, sem espaços extras. |
| "Não foi possível conectar à API" | Confirme se `api.php` e `config.php` estão na mesma pasta que `index.html` no servidor. |
| "Falha ao conectar ao banco de dados" | Revise `db_name`, `db_user` e `db_pass` em `config.php` — algum deles está errado. |
| Página em branco | Confirme se o site tem SSL ativo (hPanel → SSL → ativar, é gratuito) e acesse com `https://`. |

## Perguntas que você não precisa resolver agora

- **Login individual por vendedor:** não existe nesta versão simples — é um único
  token para toda a equipe, como uma "senha da sala". Se um dia quiser login
  por pessoa, me avise e eu construo essa parte depois.
- **Backup:** dentro do painel tem um botão **"Exportar CSV"** — use-o de vez
  em quando para guardar uma cópia dos dados fora do banco.
