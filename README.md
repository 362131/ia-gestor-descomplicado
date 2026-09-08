# Site Pessoal — Eduardo Carvalho

Site pessoal de uma página só (one-page) para Eduardo Carvalho, especialista em
Inteligência Artificial. Feito em **HTML, CSS e JavaScript puros**, sem
frameworks, sem npm e sem etapa de build.

## Arquivos

- `index.html` — estrutura e conteúdo da página.
- `style.css` — estilos, variáveis de cor, tema claro/escuro e responsividade.
- `script.js` — menu mobile, alternância de tema e ano automático no rodapé.

## Como rodar localmente

Basta abrir o arquivo `index.html` diretamente no navegador, ou servir a pasta
com qualquer servidor estático simples, por exemplo:

```bash
python3 -m http.server 8000
```

E acessar `http://localhost:8000`.

## Publicação na Cloudflare Pages

1. Suba este repositório para o GitHub (ou GitLab).
2. Na Cloudflare Pages, crie um novo projeto e conecte o repositório.
3. Como não há etapa de build, configure:
   - **Comando de build:** deixe em branco (ou `echo "sem build"`).
   - **Diretório de saída (output directory):** `/` (raiz do projeto).
4. Publique. A cada novo commit na branch principal, a Cloudflare Pages fará
   o deploy automaticamente.

## Personalização

- As cores do site ficam centralizadas em variáveis CSS no início do arquivo
  `style.css` (bloco `:root`), facilitando trocar a paleta de cores.
- O tema escuro é aplicado automaticamente conforme a preferência do sistema
  operacional do visitante, e pode ser alternado manualmente pelo botão no
  menu — a escolha fica salva no navegador (`localStorage`).
