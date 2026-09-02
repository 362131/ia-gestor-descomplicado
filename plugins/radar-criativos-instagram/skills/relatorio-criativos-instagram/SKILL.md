---
name: relatorio-criativos-instagram
description: >
  Use esta skill quando o usuário pedir "relatório de criativos do Instagram",
  "gere o radar do Instagram", "qual criativo está performando melhor no Instagram",
  "analise o desempenho do Instagram", "compare os posts do Instagram" ou
  "envie o relatório de Instagram por e-mail". Também dispare quando uma tarefa
  agendada (ex.: recorrência de segunda e quinta-feira) pedir para rodar o
  "radar de criativos do Instagram". Cobre: coleta de métricas por post via
  Windsor.ai (conector instagram, conta eduardo.carvalho.oficial), ranking dos
  criativos, comparação entre posts semelhantes (mesmo formato), geração de
  sugestões de melhoria de perfil e envio de um relatório HTML por e-mail via
  Gmail.
---

# Radar de Criativos do Instagram

## Objetivo

Ler os posts recentes da conta do Instagram **eduardo.carvalho.oficial**,
identificar qual criativo está performando melhor, comparar esse criativo com
outros de formato semelhante, e entregar ao usuário um relatório por e-mail
com o ranking e sugestões práticas de melhoria de perfil.

## Pré-requisitos (já configurados no ambiente do usuário — não pedir de novo)

- Ferramenta `Windsor.ai:get_data` com o conector `instagram` já conectado à
  conta `eduardo.carvalho.oficial` (account_id `17841402984212304`).
- Ferramenta `Gmail:send_message` já conectada à caixa do usuário.
- E-mail de destino padrão do relatório: `eduardocarvalhopalestrante@gmail.com`.
  Se o usuário pedir para enviar a outro endereço nesta execução, use o
  endereço informado no pedido em vez do padrão.

Se qualquer uma dessas ferramentas retornar erro de autenticação/permissão,
pare e avise o usuário — não tente adivinhar credenciais nem prosseguir sem
os dados reais.

## Passo 1 — Coletar os posts recentes

Chame `Windsor.ai:get_data` com:

- `connector`: `"instagram"`
- `accounts`: `["17841402984212304"]`
- `fields`: `["media_id", "media_type", "media_caption", "timestamp", "media_permalink", "media_like_count", "media_comments_count", "media_reach", "media_engagement", "media_saved", "media_shares", "media_views", "media_profile_visits", "media_follows", "media_reel_avg_watch_time", "media_reel_skip_rate", "media_reel_total_interactions"]`
- `date_from`: data de 30 dias atrás (calcule a partir da data atual)
- `date_to`: data de hoje

Se a resposta vier vazia, tente `date_preset: "last_30dT"` no lugar de
`date_from`/`date_to`. Se ainda assim vier vazia, informe ao usuário que não
há posts no período e pare — não invente dados.

## Passo 2 — Coletar o contexto do perfil (para as sugestões)

Chame `Windsor.ai:get_data` novamente com:

- `connector`: `"instagram"`
- `accounts`: `["17841402984212304"]`
- `fields`: `["date", "reach_1d", "accounts_engaged", "likes", "comments", "saves", "shares", "total_interactions", "follower_count_1d"]`
- `date_preset`: `"last_30dT"`

Use esses dados apenas como pano de fundo (tendência de alcance e de novos
seguidores no período) — o foco do relatório é o Passo 1.

## Passo 3 — Ranquear os criativos

Com os dados do Passo 1, para cada post calcule uma taxa de engajamento:

```
taxa_engajamento = media_engagement / media_reach   (quando media_reach > 0)
```

Ordene os posts por `taxa_engajamento` (desempate por `media_engagement`
absoluto). Identifique:

- **Top 1 criativo do período** (melhor taxa de engajamento).
- **Top 3** no geral.
- **Os 2 de pior desempenho**, para contraste.

## Passo 4 — Comparar criativos semelhantes

Agrupe os posts por `media_type` (`IMAGE`, `VIDEO`, `CAROUSEL_ALBUM`, `REEL`).
Dentro do grupo do criativo campeão (Passo 3), compare-o com os demais posts
do **mesmo formato** publicados no período: taxa de engajamento média do
grupo vs. a do campeão, alcance médio do grupo vs. o do campeão, e — quando
o formato for `REEL` — `media_reel_avg_watch_time` e `media_reel_skip_rate`
médios do grupo vs. os do campeão. Anote em uma frase o que diferencia o
campeão dos demais do mesmo formato (ex.: horário de publicação, presença de
legenda mais longa, gancho nos 3 primeiros segundos, uso de carrossel com
mais lâminas).

## Passo 5 — Gerar sugestões de melhoria do perfil

A partir do ranking (Passo 3), da comparação (Passo 4) e da tendência de
perfil (Passo 2), escreva de 3 a 5 sugestões práticas e específicas — nunca
genéricas. Cada sugestão deve nomear o que fazer, o motivo baseado nos dados
coletados. Exemplos de eixos a considerar: formato que mais engaja
(dobrar aposta), horário/dia de publicação dos posts com melhor alcance,
padrão de legenda ou gancho dos criativos campeões, formatos com baixo
desempenho a repensar, taxa de conversão de visualização em visita ao perfil
(`media_profile_visits`) ou em novos seguidores (`media_follows`).

Nunca invente um dado que não veio da consulta — se um eixo não tiver dado
suficiente no período, omita-o em vez de especular.

## Passo 6 — Montar o relatório em HTML

Monte um HTML autocontido (sem CSS/JS externo) com, nesta ordem:

1. Cabeçalho com o período analisado e a data de geração.
2. Card de destaque do criativo campeão: miniatura (use `media_thumbnail_url`
   se disponível, senão `media_url`), link (`media_permalink`), métricas
   principais e a taxa de engajamento.
3. Tabela com o Top 3 e os 2 piores, colunas: formato, data, alcance,
   engajamento, taxa de engajamento, link.
4. Seção "Criativos semelhantes" com a comparação do Passo 4.
5. Seção "Sugestões para o perfil" com a lista do Passo 5.

Use uma paleta simples e legível (fundo claro, um tom de destaque), sem
depender de fontes ou scripts externos.

## Passo 7 — Enviar por e-mail

Chame `Gmail:send_message` com:

- `to`: `["eduardocarvalhopalestrante@gmail.com"]` (ou o endereço indicado
  pelo usuário nesta execução, se houver)
- `subject`: `"Radar de Criativos Instagram — {data de hoje, dd/mm/aaaa}"`
- `htmlBody`: o HTML do Passo 6
- `body`: um resumo em texto puro de 3–4 linhas (criativo campeão + 1
  sugestão principal), como alternativa em texto plano

Depois de enviar, confirme ao usuário em uma frase que o relatório foi
enviado, citando o criativo campeão e o e-mail de destino usado.

## Observação sobre recorrência (segunda e quinta-feira)

Esta skill executa a análise e o envio quando é chamada — ela não tem, por
si só, um agendador embutido. Para rodar automaticamente toda segunda e
quinta-feira, o usuário precisa configurar, fora deste plugin, uma tarefa
recorrente do Claude apontando para a frase-gatilho desta skill (ex.: "gere
o radar de criativos do Instagram e envie por e-mail"), no dia e horário
desejados. Se o usuário perguntar como fazer isso, explique essa limitação
em vez de prometer um envio automático que a skill não controla sozinha.
