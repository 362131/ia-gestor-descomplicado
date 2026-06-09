import { createFileRoute } from "@tanstack/react-router";
import "@fontsource/space-grotesk/400.css";
import "@fontsource/space-grotesk/600.css";
import "@fontsource/space-grotesk/700.css";
import "@fontsource/inter/400.css";
import "@fontsource/inter/500.css";
import "@fontsource/inter/600.css";
import eduardoMendes from "@/assets/eduardo-mendes.asset.json";
import eduardoCarvalho from "@/assets/eduardo-carvalho.asset.json";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Sparkles,
  Clock,
  TrendingDown,
  ArrowRight,
  CheckCircle2,
  Calendar,
  Users,
  Zap,
} from "lucide-react";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: "IA para Gestores na Prática | Ganhe Tempo e Reduza Custos" },
      {
        name: "description",
        content:
          "Treinamento prático de Inteligência Artificial para gestores com Eduardo Mendes e Eduardo Carvalho. 12 encontros, ROI real, automação e produtividade.",
      },
      { property: "og:title", content: "IA para Gestores na Prática" },
      {
        property: "og:description",
        content:
          "Ganhe tempo e reduza custos aplicando IA na gestão. Treinamento com Eduardo Mendes e Eduardo Carvalho.",
      },
      { property: "og:image", content: eduardoMendes.url },
    ],
  }),
  component: LandingPage,
});

const topics = [
  { n: 1, t: "Introdução à IA e suas aplicações práticas", d: "Conceitos de IA e aplicações no dia a dia da gestão.", who: "Eduardo Mendes" },
  { n: 2, t: "Automação de processos com IA", d: "Como automatizar tarefas repetitivas e ganhar horas por semana.", who: "Eduardo Mendes" },
  { n: 3, t: "Análise de dados com IA", d: "Interprete grandes volumes de dados em minutos.", who: "Eduardo Mendes" },
  { n: 4, t: "IA na gestão de pessoas e recrutamento", d: "Acelere triagem, entrevistas e onboarding com IA.", who: "Eduardo Carvalho" },
  { n: 5, t: "Geração de conteúdo automatizado", d: "Crie comunicações, relatórios e propostas com IA.", who: "Eduardo Mendes" },
  { n: 6, t: "Otimização de fluxos de trabalho", d: "Identifique e elimine gargalos com IA aplicada.", who: "Eduardo Mendes" },
  { n: 7, t: "IA para atendimento ao cliente", d: "Suporte 24/7, redução de custos e aumento de NPS.", who: "Eduardo Carvalho" },
  { n: 8, t: "IA para desenvolvimento de lideranças", d: "Ferramentas para acelerar líderes da sua organização.", who: "Eduardo Carvalho" },
  { n: 9, t: "Processamento de documentos com IA", d: "Extraia informação de contratos, notas e PDFs.", who: "Eduardo Mendes" },
  { n: 10, t: "Integração de ferramentas de IA", d: "Conecte as melhores ferramentas no seu stack.", who: "Eduardo Mendes" },
  { n: 11, t: "Casos de sucesso e ROI", d: "Estudos reais de retorno sobre investimento em IA.", who: "Eduardo Mendes" },
  { n: 12, t: "IA para cultura e engajamento", d: "Promova engajamento e cultura organizacional com IA.", who: "Eduardo Carvalho" },
];

function LandingPage() {
  return (
    <div className="min-h-screen" style={{ background: "var(--gradient-hero)" }}>
      {/* Nav */}
      <header className="mx-auto flex max-w-7xl items-center justify-between px-6 py-6">
        <div className="flex items-center gap-2 font-display text-lg font-semibold">
          <Sparkles className="h-5 w-5 text-primary" />
          IA na Prática
        </div>
        <a href="#inscricao">
          <Button variant="default" className="bg-primary text-primary-foreground hover:opacity-90">
            Quero participar
          </Button>
        </a>
      </header>

      {/* Hero */}
      <section className="mx-auto max-w-7xl px-6 pt-12 pb-24 md:pt-20">
        <div className="grid items-center gap-12 md:grid-cols-2">
          <div>
            <Badge className="mb-5 bg-secondary text-secondary-foreground border-border">
              <Zap className="mr-1 h-3 w-3 text-primary" /> Turma 2026 · Vagas limitadas
            </Badge>
            <h1 className="font-display text-4xl font-bold leading-[1.05] md:text-6xl">
              Inteligência Artificial para Gestores{" "}
              <span
                className="bg-clip-text text-transparent"
                style={{ backgroundImage: "var(--gradient-gold)" }}
              >
                na Prática
              </span>
            </h1>
            <p className="mt-5 text-xl text-muted-foreground md:text-2xl">
              Ganhe tempo e reduza custos.
            </p>
            <p className="mt-6 max-w-xl text-base text-muted-foreground">
              Um programa direto ao ponto para gestores que querem aplicar IA em
              decisões, operações e pessoas — sem teoria desnecessária.
            </p>

            <div className="mt-8 flex flex-wrap items-center gap-4">
              <a href="#inscricao">
                <Button
                  size="lg"
                  className="bg-primary text-primary-foreground hover:opacity-90"
                  style={{ boxShadow: "var(--shadow-glow)" }}
                >
                  Garantir minha vaga <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </a>
              <a href="#temas" className="text-sm font-medium text-muted-foreground hover:text-foreground">
                Ver os 12 temas →
              </a>
            </div>

            <div className="mt-10 grid grid-cols-3 gap-4 max-w-md">
              <Stat icon={<Calendar className="h-4 w-4" />} v="12" l="encontros" />
              <Stat icon={<Clock className="h-4 w-4" />} v="3 meses" l="duração" />
              <Stat icon={<Users className="h-4 w-4" />} v="2" l="experts" />
            </div>
          </div>

          <div className="relative">
            <div
              className="absolute -inset-6 rounded-3xl opacity-30 blur-3xl"
              style={{ background: "var(--gradient-gold)" }}
            />
            <div className="relative grid grid-cols-2 gap-4">
              <SpeakerCard
                img={eduardoMendes.url}
                name="Eduardo Mendes"
                share="80%"
                tall
              />
              <div className="flex flex-col gap-4">
                <SpeakerCard
                  img={eduardoCarvalho.url}
                  name="Eduardo Carvalho"
                  share="20%"
                />
                <Card className="bg-card/80 border-border p-4 backdrop-blur">
                  <div className="flex items-center gap-2 text-sm font-semibold">
                    <TrendingDown className="h-4 w-4 text-primary" />
                    Resultados reais
                  </div>
                  <p className="mt-2 text-xs text-muted-foreground">
                    Cases de ROI, automação e redução de custos em cada módulo.
                  </p>
                </Card>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Benefits */}
      <section className="mx-auto max-w-7xl px-6 pb-24">
        <div className="grid gap-4 md:grid-cols-3">
          {[
            { i: <Clock className="h-5 w-5" />, t: "Ganhe horas por semana", d: "Automatize tarefas repetitivas e libere foco para o estratégico." },
            { i: <TrendingDown className="h-5 w-5" />, t: "Reduza custos operacionais", d: "Otimize fluxos e elimine retrabalho com IA aplicada." },
            { i: <Sparkles className="h-5 w-5" />, t: "Decida com mais dados", d: "Análises rápidas e relatórios prontos em minutos." },
          ].map((b) => (
            <Card key={b.t} className="bg-card/60 border-border p-6 backdrop-blur">
              <div className="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-primary/15 text-primary">
                {b.i}
              </div>
              <h3 className="font-display text-lg font-semibold">{b.t}</h3>
              <p className="mt-2 text-sm text-muted-foreground">{b.d}</p>
            </Card>
          ))}
        </div>
      </section>

      {/* Speakers full */}
      <section className="mx-auto max-w-7xl px-6 pb-24">
        <h2 className="font-display text-3xl font-bold md:text-4xl">Quem ministra</h2>
        <p className="mt-2 text-muted-foreground">
          Dois Eduardos, uma missão: IA que gera resultado para o gestor.
        </p>

        <div className="mt-10 grid gap-6 md:grid-cols-2">
          <SpeakerBio
            img={eduardoMendes.url}
            name="Eduardo Mendes"
            role="Especialista em IA aplicada"
            bio="Consultor e instrutor de Inteligência Artificial para negócios, com vasta experiência em automação de processos, análise de dados e implementação de IA em empresas de diversos setores. Conduz a maior parte do treinamento, trazendo ferramentas, demonstrações ao vivo e cases reais."
            highlights={[
              "Automação de processos e fluxos de trabalho",
              "Análise de dados e geração de conteúdo com IA",
              "Integração de ferramentas e cases de ROI",
            ]}
          />
          <SpeakerBio
            img={eduardoCarvalho.url}
            name="Eduardo Carvalho"
            role="Especialista em Gestão de Pessoas"
            bio="Líder com longa trajetória em gestão de pessoas, desenvolvimento de lideranças e cultura organizacional. Traz para o programa a visão de como aplicar IA em RH, atendimento e engajamento — conectando tecnologia com o lado humano da gestão."
            highlights={[
              "IA na gestão de pessoas e recrutamento",
              "Desenvolvimento de lideranças apoiado por IA",
              "Cultura organizacional e engajamento",
            ]}
          />
        </div>
      </section>

      {/* Topics */}
      <section id="temas" className="mx-auto max-w-7xl px-6 pb-24">
        <div className="mb-10 flex items-end justify-between gap-4 flex-wrap">
          <div>
            <h2 className="font-display text-3xl font-bold md:text-4xl">Os 12 temas do programa</h2>
            <p className="mt-2 text-muted-foreground">3 meses · 4 encontros por mês · 100% prático</p>
          </div>
          <div className="flex items-center gap-3 text-xs text-muted-foreground">
            <LegendDot label="Eduardo Mendes" color="var(--color-primary)" />
            <LegendDot label="Eduardo Carvalho" color="var(--color-accent)" />
          </div>
        </div>

        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {topics.map((tp) => (
            <Card
              key={tp.n}
              className="group relative overflow-hidden bg-card/60 border-border p-6 backdrop-blur transition hover:-translate-y-1 hover:border-primary/40"
            >
              <div
                className="absolute inset-x-0 top-0 h-1"
                style={{
                  background:
                    tp.who === "Eduardo Mendes"
                      ? "var(--gradient-gold)"
                      : "var(--color-accent)",
                }}
              />
              <div className="flex items-center justify-between">
                <span className="font-display text-3xl font-bold text-muted-foreground/40">
                  {String(tp.n).padStart(2, "0")}
                </span>
                <span className="text-[11px] uppercase tracking-wider text-muted-foreground">
                  {tp.who}
                </span>
              </div>
              <h3 className="mt-3 font-display text-lg font-semibold leading-snug">
                {tp.t}
              </h3>
              <p className="mt-2 text-sm text-muted-foreground">{tp.d}</p>
            </Card>
          ))}
        </div>
      </section>

      {/* CTA */}
      <section id="inscricao" className="mx-auto max-w-5xl px-6 pb-24">
        <Card
          className="relative overflow-hidden border-border p-10 md:p-16 text-center"
          style={{ background: "var(--gradient-hero)" }}
        >
          <div
            className="absolute -top-32 left-1/2 h-64 w-[140%] -translate-x-1/2 rounded-full opacity-30 blur-3xl"
            style={{ background: "var(--gradient-gold)" }}
          />
          <div className="relative">
            <Badge className="mb-5 bg-primary/15 text-primary border-primary/30">
              Vagas limitadas
            </Badge>
            <h2 className="font-display text-3xl font-bold md:text-5xl">
              Pronto para liderar com IA?
            </h2>
            <p className="mx-auto mt-4 max-w-xl text-muted-foreground">
              Garanta sua vaga no treinamento e comece a transformar sua gestão
              com Inteligência Artificial aplicada.
            </p>
            <div className="mt-8 flex flex-wrap justify-center gap-4">
              <a href="https://wa.me/?text=Quero%20participar%20do%20treinamento%20IA%20para%20Gestores%20na%20Pr%C3%A1tica" target="_blank" rel="noreferrer">
                <Button
                  size="lg"
                  className="bg-primary text-primary-foreground hover:opacity-90"
                  style={{ boxShadow: "var(--shadow-glow)" }}
                >
                  Quero minha vaga <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </a>
            </div>
            <div className="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-muted-foreground">
              <span className="inline-flex items-center gap-2">
                <CheckCircle2 className="h-4 w-4 text-primary" /> Certificado de conclusão
              </span>
              <span className="inline-flex items-center gap-2">
                <CheckCircle2 className="h-4 w-4 text-primary" /> Material e ferramentas
              </span>
              <span className="inline-flex items-center gap-2">
                <CheckCircle2 className="h-4 w-4 text-primary" /> Aulas práticas ao vivo
              </span>
            </div>
          </div>
        </Card>
      </section>

      <footer className="border-t border-border">
        <div className="mx-auto max-w-7xl px-6 py-8 text-center text-sm text-muted-foreground">
          © 2026 IA para Gestores na Prática · Eduardo Mendes & Eduardo Carvalho
        </div>
      </footer>
    </div>
  );
}

function Stat({ icon, v, l }: { icon: React.ReactNode; v: string; l: string }) {
  return (
    <div className="rounded-xl border border-border bg-card/40 p-3 backdrop-blur">
      <div className="flex items-center gap-1.5 text-primary">{icon}<span className="font-display text-lg font-bold text-foreground">{v}</span></div>
      <div className="text-[11px] uppercase tracking-wider text-muted-foreground">{l}</div>
    </div>
  );
}

function SpeakerCard({ img, name, share: _share, tall }: { img: string; name: string; share?: string; tall?: boolean }) {
  return (
    <Card className="relative overflow-hidden border-border bg-card p-0">
      <div className={tall ? "aspect-[3/4]" : "aspect-square"}>
        <img src={img} alt={name} className="h-full w-full object-cover" />
      </div>
      <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-3">
        <div className="text-sm font-semibold text-white">{name}</div>
      </div>
    </Card>
  );
}

function SpeakerBio({
  img,
  name,
  role,
  bio,
  highlights,
}: {
  img: string;
  name: string;
  role: string;
  bio: string;
  highlights: string[];
}) {
  return (
    <Card className="overflow-hidden bg-card/60 border-border backdrop-blur">
      <div className="grid grid-cols-[140px_1fr] gap-5 p-6 md:grid-cols-[180px_1fr]">
        <div className="aspect-[3/4] overflow-hidden rounded-lg bg-secondary">
          <img src={img} alt={name} className="h-full w-full object-cover object-top" />
        </div>
        <div>
          <h3 className="font-display text-xl font-bold">{name}</h3>
          <div className="mt-1 text-xs uppercase tracking-wider text-primary">{role}</div>
          <p className="mt-3 text-sm text-muted-foreground">{bio}</p>
          <ul className="mt-4 space-y-1.5">
            {highlights.map((h) => (
              <li key={h} className="flex gap-2 text-sm">
                <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                <span>{h}</span>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </Card>
  );
}

function LegendDot({ label, color }: { label: string; color: string }) {
  return (
    <span className="inline-flex items-center gap-1.5">
      <span className="inline-block h-2.5 w-2.5 rounded-full" style={{ background: color }} />
      {label}
    </span>
  );
}
