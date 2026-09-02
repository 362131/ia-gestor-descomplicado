# Radar de Criativos Instagram

## Visão geral

Plugin pessoal para o Eduardo analisar o desempenho dos posts do Instagram
(@eduardo.carvalho.oficial), descobrir qual criativo está performando
melhor, comparar esse criativo com outros do mesmo formato, e receber por
e-mail um relatório em HTML com o ranking e sugestões de melhoria de perfil.

## Componentes

| Componente | Quantidade | Propósito |
|---|---|---|
| Skills | 1 (`relatorio-criativos-instagram`) | Coleta os dados do Instagram via Windsor.ai, ranqueia os criativos, compara posts semelhantes, gera sugestões e envia o relatório por Gmail. |
| Agents | 0 | Não necessário — o fluxo é único e sequencial, coberto pela skill. |
| Hooks | 0 | Não há evento determinístico a interceptar. Agendamento por dia da semana não é um evento de hook — veja "Recorrência" abaixo. |
| MCP | 0 (arquivo `.mcp.json` não incluído) | Windsor.ai e Gmail já são conectores nativos deste ambiente, já autorizados na conta do usuário. A skill os referencia diretamente pelo nome. |

## Configuração

- Windsor.ai precisa ter o conector `instagram` conectado com a conta
  `eduardo.carvalho.oficial` (account_id `17841402984212304` — já conectado
  no momento da criação deste plugin). Se a conta for reconectada e o
  `account_id` mudar, atualize o valor fixo no Passo 1 do arquivo
  `skills/relatorio-criativos-instagram/SKILL.md`.
- Gmail precisa estar conectado à caixa de onde o relatório será enviado.
- E-mail de destino padrão: `eduardocarvalhopalestrante@gmail.com` (fixo no
  corpo da skill — troque lá se o destino mudar).
- Nenhuma credencial fica gravada no plugin: as duas ferramentas usam a
  conexão já autorizada pelo usuário no ambiente do Claude.

## Uso

Dispare a skill com uma frase como:

> "Gere o radar de criativos do Instagram e envie por e-mail."

ou

> "Qual criativo está performando melhor no Instagram esse mês?"

A skill coleta os últimos 30 dias de posts, monta o relatório e envia por
e-mail, confirmando o resultado na conversa.

## Recorrência (segunda e quinta-feira)

Este plugin não tem agendador embutido — o schema de plugin do Claude
Code/Cowork não oferece um evento de hook baseado em tempo/cron, só eventos
como início de sessão ou uso de ferramenta. Para o relatório sair
automaticamente toda segunda e quinta-feira, configure, fora deste plugin,
uma tarefa recorrente do Claude (feature de agendamento do próprio
Claude/Cowork) apontando para a frase-gatilho da skill, nos dias e horário
desejados. Sem essa tarefa recorrente configurada, a skill só roda quando
alguém pedir na conversa.

## Personalização

Este plugin é de uso pessoal e cita as ferramentas Windsor.ai e Gmail
diretamente (não usa ~~categoria), então não há `CONNECTORS.md`. Para
adaptar a outra conta de Instagram ou outro e-mail de destino, edite os
valores fixos indicados na seção "Configuração" acima, diretamente no
arquivo `skills/relatorio-criativos-instagram/SKILL.md`.
