
import {
  CalendarCheck,
  CalendarClock,
  Users,
  BriefcaseBusiness,
  ArrowRight,
  Plus,
} from "lucide-react";

import { AppShell } from "@/components/layout/app-shell";
import { StatCard } from "@/components/dashboard/stat-card";
import { WeeklyOverview } from "@/components/dashboard/weekly-overview";
import { AppointmentsList } from "@/components/dashboard/appointments-list";

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

        {/* Indicadores */}
        <section className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <StatCard
            title="Agendamentos"
            value="128"
            description="em relação ao período anterior"
            trend="+12%"
            icon={CalendarCheck}
            color="blue"
          />

          <StatCard
            title="Consultas de hoje"
            value="18"
            description="atendimentos programados"
            icon={CalendarClock}
            color="green"
          />

          <StatCard
            title="Clientes cadastrados"
            value="342"
            description="cadastros ilustrativos"
            trend="+8%"
            icon={Users}
            color="purple"
          />

          <StatCard
            title="Serviços ativos"
            value="12"
            description="serviços disponíveis"
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
              Este painel utiliza dados fictícios e não representa registros reais.
            </p>
          </div>

          <span className="inline-flex items-center gap-1 text-xs font-semibold text-blue-700">
            Projeto AW TECHNOLOGY
            <ArrowRight size={14} />
          </span>
        </div>
      </div>
    </AppShell>
  );
}
