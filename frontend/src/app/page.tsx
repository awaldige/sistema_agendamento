
import { Suspense } from "react";

import {
  CalendarCheck,
  CalendarClock,
  Users,
  BriefcaseBusiness,
  ArrowRight,
  Plus,
} from "lucide-react";

import { prisma } from "@/lib/prisma";
import { AppShell } from "@/components/layout/app-shell";
import { StatCard } from "@/components/dashboard/stat-card";
import { WeeklyOverview } from "@/components/dashboard/weekly-overview";
import { AppointmentsList } from "@/components/dashboard/appointments-list";

async function DashboardContent() {
  // Obtém a data atual diretamente do PostgreSQL,
  // considerando o fuso horário de São Paulo.
  const resultadoData = await prisma.$queryRaw<{ hoje: Date }[]>`
    SELECT (CURRENT_TIMESTAMP AT TIME ZONE 'America/Sao_Paulo')::date AS hoje
  `;

  const hoje = resultadoData[0].hoje;

  const inicioHoje = new Date(
    Date.UTC(
      hoje.getUTCFullYear(),
      hoje.getUTCMonth(),
      hoje.getUTCDate()
    )
  );

  const inicioAmanha = new Date(
    Date.UTC(
      hoje.getUTCFullYear(),
      hoje.getUTCMonth(),
      hoje.getUTCDate() + 1
    )
  );

  // Consulta os indicadores diretamente no banco.
  const [
    totalAgendamentos,
    consultasHoje,
    pacientesUnicos,
    totalServicos,
  ] = await Promise.all([
    prisma.agendamento.count(),

    prisma.agendamento.count({
      where: {
        data: {
          gte: inicioHoje,
          lt: inicioAmanha,
        },
      },
    }),

    prisma.agendamento.groupBy({
      by: ["paciente"],
    }),

    prisma.servico.count(),
  ]);

  return (
    <>
      {/* Indicadores reais */}
      <section className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard
          title="Agendamentos"
          value={String(totalAgendamentos)}
          description="registros cadastrados"
          icon={CalendarCheck}
          color="blue"
        />

        <StatCard
          title="Consultas de hoje"
          value={String(consultasHoje)}
          description="atendimentos programados"
          icon={CalendarClock}
          color="green"
        />

        <StatCard
          title="Clientes cadastrados"
          value={String(pacientesUnicos.length)}
          description="pacientes únicos"
          icon={Users}
          color="purple"
        />

        <StatCard
          title="Serviços ativos"
          value={String(totalServicos)}
          description="serviços cadastrados"
          icon={BriefcaseBusiness}
          color="orange"
        />
      </section>

      {/* Gráfico semanal */}
      <section>
        <WeeklyOverview />
      </section>

      {/* Agendamentos */}
      <section>
        <AppointmentsList />
      </section>

      {/* Rodapé informativo */}
      <div className="flex flex-col items-start justify-between gap-2 rounded-xl border border-blue-100 bg-blue-50/70 p-4 sm:flex-row sm:items-center">
        <div>
          <p className="text-sm font-semibold text-blue-900">
            Ambiente de demonstração
          </p>

          <p className="mt-1 text-xs text-blue-700">
            Os indicadores apresentam os registros cadastrados no sistema.
          </p>
        </div>

        <span className="inline-flex items-center gap-1 text-xs font-semibold text-blue-700">
          Projeto AW TECHNOLOGY
          <ArrowRight size={14} />
        </span>
      </div>
    </>
  );
}

export default function Home() {
  return (
    <AppShell>
      <div className="mx-auto max-w-7xl space-y-7">
        {/* Título do dashboard */}
        <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div>
            <div className="flex flex-wrap items-center gap-2">
              <h1 className="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
                Visão geral
              </h1>

              <span className="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">
                Demonstração
              </span>
            </div>

            <p className="mt-2 text-sm text-slate-500">
              Acompanhe os principais indicadores do seu sistema.
            </p>
          </div>

          <button
            type="button"
            disabled
            title="Funcionalidade demonstrativa ainda não implementada"
            className="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white opacity-70 shadow-sm"
          >
            <Plus size={18} />
            Novo agendamento
          </button>
        </section>

        <Suspense
          fallback={
            <div className="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
              Carregando indicadores e agendamentos...
            </div>
          }
        >
          <DashboardContent />
        </Suspense>
      </div>
    </AppShell>
  );
}
