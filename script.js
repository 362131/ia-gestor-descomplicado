// ============================================================
// script.js
// Todo o comportamento interativo da página está aqui:
// menu mobile, alternância de tema claro/escuro e ano no rodapé.
// ============================================================

// Espera o HTML inteiro carregar antes de rodar o script,
// garantindo que todos os elementos já existem na página.
document.addEventListener("DOMContentLoaded", () => {

  // ----------------------------------------------------------
  // 1) MENU MOBILE (as três risquinhas)
  // ----------------------------------------------------------
  // Pegamos o botão hambúrguer e a lista de links do menu.
  const botaoMenu = document.getElementById("menu-toggle");
  const listaMenu = document.getElementById("nav-links");

  botaoMenu.addEventListener("click", () => {
    // A classe "aberto" é quem controla, via CSS, se o menu aparece
    // ou fica escondido no celular (ver style.css, seção responsividade).
    listaMenu.classList.toggle("aberto");

    // Atualiza o atributo de acessibilidade para leitores de tela
    // saberem se o menu está aberto ou fechado.
    const estaAberto = listaMenu.classList.contains("aberto");
    botaoMenu.setAttribute("aria-expanded", estaAberto);
  });

  // Fecha o menu automaticamente quando o usuário clica em um link,
  // para não ficar aberto "sujando" a tela depois da navegação.
  const linksDoMenu = listaMenu.querySelectorAll("a");
  linksDoMenu.forEach((link) => {
    link.addEventListener("click", () => {
      listaMenu.classList.remove("aberto");
      botaoMenu.setAttribute("aria-expanded", "false");
    });
  });

  // ----------------------------------------------------------
  // 2) ALTERNÂNCIA DE TEMA CLARO/ESCURO
  // ----------------------------------------------------------
  const botaoTema = document.getElementById("theme-toggle");
  const CHAVE_STORAGE = "tema-preferido"; // nome usado para salvar a escolha no navegador

  // Função que aplica o tema escuro ou claro na página,
  // trocando a classe do <body> e o ícone do botão.
  function aplicarTema(tema) {
    if (tema === "escuro") {
      document.body.classList.add("tema-escuro");
      botaoTema.textContent = "☀️"; // mostra ícone de sol, indicando que pode voltar ao claro
    } else {
      document.body.classList.remove("tema-escuro");
      botaoTema.textContent = "🌙"; // mostra ícone de lua, indicando que pode ir para o escuro
    }
  }

  // Ao carregar a página, verifica se o usuário já escolheu um tema
  // antes (salvo no localStorage). Se não escolheu, usa a preferência
  // do sistema operacional/navegador como padrão.
  const temaSalvo = localStorage.getItem(CHAVE_STORAGE);
  const prefereEscuroNoSistema = window.matchMedia("(prefers-color-scheme: dark)").matches;

  if (temaSalvo) {
    aplicarTema(temaSalvo);
  } else if (prefereEscuroNoSistema) {
    aplicarTema("escuro");
  }

  // Ao clicar no botão, alterna entre os temas e salva a escolha,
  // para que ela seja lembrada da próxima vez que o usuário visitar o site.
  botaoTema.addEventListener("click", () => {
    const temaAtualEhEscuro = document.body.classList.contains("tema-escuro");
    const novoTema = temaAtualEhEscuro ? "claro" : "escuro";

    aplicarTema(novoTema);
    localStorage.setItem(CHAVE_STORAGE, novoTema);
  });

  // ----------------------------------------------------------
  // 3) ANO ATUAL NO RODAPÉ
  // ----------------------------------------------------------
  // Preenche automaticamente o ano no rodapé, para não precisar
  // atualizar o HTML manualmente todo ano.
  const spanAno = document.getElementById("ano-atual");
  spanAno.textContent = new Date().getFullYear();
});
