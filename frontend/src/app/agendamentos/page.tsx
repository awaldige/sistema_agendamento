
"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import {
  CalendarDays,
  Clock3,
  Search,
  Plus,
  Pencil,
  Trash2,
  ChevronLeft,
  ChevronRight,
  FileText,
} from "lucide-react";

import { AppShell } from "@/components/layout/app-shell";

interface Appointment {
  id: number;
  paciente: string;
  email: string | null;
  telefone: string | null;
  data: string;
  hora: string;
  servico: {
    id: number;
    nome: string;
  };
  tipo: "particular" | "convenio";
}

const months = [
  "Janeiro",
  "Fevereiro",
  "Março",
  "Abril",
  "Maio",
  "Junho",
  "Julho",
  "Agosto",
  "Setembro",
  "Outubro",
  "Novembro",
  "Dezembro",
];

function formatDate(date: string) {
  return new Date(`${date.slice(0, 10)}T12:00:00`).toLocaleDateString(
    "pt-BR"
  );
}

export default function AppointmentsPage() {
  const [search, setSearch] = useState("");
  const [month, setMonth] = useState("11");
  const [year, setYear] = useState("2026");
  const [page, setPage] = useState(1);

  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState("");
  const [excluindoId, setExcluindoId] = useState<number | null>(null);
  const [mensagem, setMensagem] = useState("");

  useEffect(() => {
    async function carregarAgendamentos() {
      try {
        const resposta = await fetch("/api/agendamentos", {
          cache: "no-store",
        });

        const dados = await resposta.json();

        if (!resposta.ok) {
          throw new Error(
            dados.erro || "Não foi possível carregar os agendamentos."
          );
        }

        setAppointments(dados);
        setErro("");
      } catch (error) {
        console.error("Erro ao carregar agendamentos:", error);

        setErro(
          error instanceof Error
            ? error.message
            : "Ocorreu um erro ao carregar os agendamentos."
        );
      } finally {
        setCarregando(false);
      }
    }

    carregarAgendamentos();
  }, []);

  async function excluirAgendamento(appointment: Appointment) {
    const confirmado = window.confirm(
      `Deseja realmente excluir o agendamento de ${appointment.paciente}?`
    );

    if (!confirmado || excluindoId !== null) return;

    setExcluindoId(appointment.id);
    setMensagem("");
    setErro("");

    try {
      const resposta = await fetch(
        `/api/agendamentos/${appointment.id}`,
        {
          method: "DELETE",
        }
      );

      const resultado = await resposta.json();

      if (!resposta.ok) {
        throw new Error(
          resultado.erro || "Não foi possível excluir o agendamento."
        );
      }

      setAppointments((atuais) =>
        atuais.filter((item) => item.id !== appointment.id)
      );

      setMensagem("Agendamento excluído com sucesso.");
    } catch (error) {
      console.error("Erro ao excluir agendamento:", error);

      setErro(
        error instanceof Error
          ? error.message
          : "Ocorreu um erro ao excluir o agendamento."
      );
    } finally {
      setExcluindoId(null);
    }
  }

  const filteredAppointments = useMemo(() => {
    if (carregando || erro) return [];

    return appointments
      .filter((appointment) => {
        const matchesName = appointment.paciente
          .toLowerCase()
          .includes(search.toLowerCase());

        const appointmentDate = appointment.data.slice(0, 10);
        const [appointmentYear, appointmentMonth] =
          appointmentDate.split("-");

        return (
          matchesName &&
          appointmentMonth === month &&
          appointmentYear === year
        );
      })
      .sort((a, b) =>
        `${a.data} ${a.hora}`.localeCompare(`${b.data} ${b.hora}`)
      );
  }, [appointments, carregando, erro, search, month, year]);

  const pageSize = 5;

  const totalPages = Math.max(
    1,
    Math.ceil(filteredAppointments.length / pageSize)
  );

  const visibleAppointments = filteredAppointments.slice(
    (page - 1) * pageSize,
    page * pageSize
  );

  function changeMonth(value: string) {
    setMonth(value);
    setPage(1);
  }

  function changeYear(value: string) {
    setYear(value);
    setPage(1);
  }

  function changeSearch(value: string) {
    setSearch(value);
    setPage(1);
  }

  return (
    <AppShell>
      <div className="mx-auto w-full max-w-7xl min-w-0 space-y-6">
        <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
              Agendamentos
            </h1>
            <p className="mt-2 text-sm text-slate-500">
              Consulte e organize os agendamentos cadastrados.
            </p>
          </div>

          <Link
            href="/agendamentos/novo"
            className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
          >
            <Plus size={18} />
            Novo agendamento
          </Link>
        </section>

        <section className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p className="text-sm text-slate-500">Total no período</p>
            <p className="mt-2 text-2xl font-bold text-slate-800">
              {filteredAppointments.length}
            </p>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p className="text-sm text-slate-500">Particulares</p>
            <p className="mt-2 text-2xl font-bold text-slate-800">
              {filteredAppointments.filter(
                (item) => item.tipo === "particular"
              ).length}
            </p>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p className="text-sm text-slate-500">Convênios</p>
            <p className="mt-2 text-2xl font-bold text-slate-800">
              {filteredAppointments.filter(
                (item) => item.tipo === "convenio"
              ).length}
            </p>
          </div>
        </section>

        <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
          <div className="mb-5 flex items-center gap-2">
            <FileText size={19} className="text-blue-600" />
            <h2 className="font-bold text-slate-800">
              Consultar agendamentos
            </h2>
          </div>

          <div className="grid grid-cols-1 gap-4 md:grid-cols-[minmax(200px,1fr)_180px_130px]">
            <div className="min-w-0">
              <label
                htmlFor="search"
                className="mb-2 block text-xs font-semibold text-slate-600"
              >
                Buscar paciente
              </label>

              <div className="relative">
                <Search
                  size={18}
                  className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                />
                <input
                  id="search"
                  type="search"
                  value={search}
                  onChange={(event) => changeSearch(event.target.value)}
                  placeholder="Digite o nome do paciente"
                  className="w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                />
              </div>
            </div>

            <div>
              <label
                htmlFor="month"
                className="mb-2 block text-xs font-semibold text-slate-600"
              >
                Mês
              </label>

              <select
                id="month"
                value={month}
                onChange={(event) => changeMonth(event.target.value)}
                className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500"
              >
                {months.map((name, index) => (
                  <option
                    key={name}
                    value={String(index + 1).padStart(2, "0")}
                  >
                    {name}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label
                htmlFor="year"
                className="mb-2 block text-xs font-semibold text-slate-600"
              >
                Ano
              </label>

              <select
                id="year"
                value={year}
                onChange={(event) => changeYear(event.target.value)}
                className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500"
              >
                <option value="2026">2026</option>
                <option value="2025">2025</option>
              </select>
            </div>
          </div>
        </section>

        {mensagem && (
          <div
            role="status"
            className="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
          >
            {mensagem}
          </div>
        )}

        {erro && (
          <div
            role="alert"
            className="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
          >
            {erro}
          </div>
        )}

        <section className="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div className="flex flex-col justify-between gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
            <div>
              <h2 className="font-bold text-slate-800">
                Lista de agendamentos
              </h2>
              <p className="mt-1 text-xs text-slate-500">
                {carregando
                  ? "Carregando registros..."
                  : `${filteredAppointments.length} registro(s) encontrado(s)`}
              </p>
            </div>

            <span className="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
              {months[Number(month) - 1]} de {year}
            </span>
          </div>

          {/* Cards para dispositivos móveis */}
          <div className="space-y-3 p-4 md:hidden">
            {carregando && (
              <p className="py-8 text-center text-sm text-slate-500">
                Carregando agendamentos...
              </p>
            )}

            {!carregando &&
              !erro &&
              visibleAppointments.map((appointment) => (
                <article
                  key={appointment.id}
                  className="min-w-0 rounded-xl border border-slate-200 bg-white p-4"
                >
                  <div className="flex min-w-0 items-start justify-between gap-3">
                    <div className="min-w-0">
                      <h3 className="break-words text-sm font-bold text-slate-800">
                        {appointment.paciente}
                      </h3>
                      {appointment.email && (
                        <p className="mt-1 break-all text-xs text-slate-400">
                          {appointment.email}
                        </p>
                      )}
                    </div>

                    <span
                      className={`shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold ${
                        appointment.tipo === "particular"
                          ? "bg-blue-50 text-blue-700"
                          : "bg-purple-50 text-purple-700"
                      }`}
                    >
                      {appointment.tipo === "particular"
                        ? "Particular"
                        : "Convênio"}
                    </span>
                  </div>

                  <div className="mt-4 space-y-2 border-t border-slate-100 pt-3">
                    <div className="flex items-center gap-2 text-sm text-slate-600">
                      <CalendarDays
                        size={16}
                        className="shrink-0 text-blue-500"
                      />
                      <span>{formatDate(appointment.data)}</span>
                    </div>

                    <div className="flex items-center gap-2 text-sm text-slate-600">
                      <Clock3
                        size={16}
                        className="shrink-0 text-blue-500"
                      />
                      <span>{appointment.hora}</span>
                    </div>

                    <p className="break-words text-sm text-slate-600">
                      <span className="font-semibold text-slate-700">
                        Serviço:
                      </span>{" "}
                      {appointment.servico.nome}
                    </p>
                  </div>

                  <div className="mt-4 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                    <Link
                      href={`/agendamentos/${appointment.id}`}
                      title="Editar agendamento"
                      aria-label={`Editar agendamento de ${appointment.paciente}`}
                      className="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
                    >
                      <Pencil size={15} />
                      Editar
                    </Link>

                    <button
                      type="button"
                      onClick={() => excluirAgendamento(appointment)}
                      disabled={excluindoId !== null}
                      title="Excluir agendamento"
                      aria-label={`Excluir agendamento de ${appointment.paciente}`}
                      className="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                      {excluindoId === appointment.id ? (
                        <span className="h-[15px] w-[15px] animate-spin rounded-full border-2 border-red-300 border-t-red-600" />
                      ) : (
                        <Trash2 size={15} />
                      )}
                      Excluir
                    </button>
                  </div>
                </article>
              ))}

            {!carregando &&
              !erro &&
              visibleAppointments.length === 0 && (
                <div className="py-10 text-center">
                  <CalendarDays
                    size={32}
                    className="mx-auto mb-3 text-slate-300"
                  />
                  <p className="text-sm font-semibold text-slate-600">
                    Nenhum agendamento encontrado
                  </p>
                  <p className="mt-1 text-xs text-slate-400">
                    Experimente alterar o nome, mês ou ano.
                  </p>
                </div>
              )}
          </div>

          {/* Tabela para tablets e computadores */}
          <div className="hidden overflow-x-auto md:block">
            <table className="w-full min-w-[760px] text-left">
              <thead className="bg-slate-50">
                <tr className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                  <th className="px-6 py-4">Paciente</th>
                  <th className="px-6 py-4">Data e hora</th>
                  <th className="px-6 py-4">Serviço</th>
                  <th className="px-6 py-4">Tipo</th>
                  <th className="px-6 py-4 text-right">Ações</th>
                </tr>
              </thead>

              <tbody className="divide-y divide-slate-100">
                {visibleAppointments.map((appointment) => (
                  <tr
                    key={appointment.id}
                    className="transition hover:bg-slate-50"
                  >
                    <td className="px-6 py-4">
                      <p className="text-sm font-semibold text-slate-800">
                        {appointment.paciente}
                      </p>
                      <p className="mt-1 text-xs text-slate-400">
                        {appointment.email}
                      </p>
                    </td>

                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm text-slate-700">
                        <CalendarDays
                          size={15}
                          className="text-blue-500"
                        />
                        {formatDate(appointment.data)}
                      </div>
                      <div className="mt-1 flex items-center gap-2 text-xs text-slate-400">
                        <Clock3 size={14} />
                        {appointment.hora}
                      </div>
                    </td>

                    <td className="px-6 py-4 text-sm text-slate-600">
                      {appointment.servico.nome}
                    </td>

                    <td className="px-6 py-4">
                      <span
                        className={`rounded-full px-3 py-1.5 text-xs font-semibold ${
                          appointment.tipo === "particular"
                            ? "bg-blue-50 text-blue-700"
                            : "bg-purple-50 text-purple-700"
                        }`}
                      >
                        {appointment.tipo === "particular"
                          ? "Particular"
                          : "Convênio"}
                      </span>
                    </td>

                    <td className="px-6 py-4">
                      <div className="flex justify-end gap-2">
                        <Link
                          href={`/agendamentos/${appointment.id}`}
                          title="Editar agendamento"
                          className="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50"
                        >
                          <Pencil size={17} />
                        </Link>

                        <button
                          type="button"
                          onClick={() => excluirAgendamento(appointment)}
                          disabled={excluindoId !== null}
                          title="Excluir agendamento"
                          aria-label={`Excluir agendamento de ${appointment.paciente}`}
                          className="rounded-lg p-2 text-red-500 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                          {excluindoId === appointment.id ? (
                            <span className="block h-[17px] w-[17px] animate-spin rounded-full border-2 border-red-300 border-t-red-600" />
                          ) : (
                            <Trash2 size={17} />
                          )}
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}

                {carregando && (
                  <tr>
                    <td
                      colSpan={5}
                      className="px-6 py-14 text-center text-sm text-slate-500"
                    >
                      Carregando agendamentos...
                    </td>
                  </tr>
                )}

                {!carregando &&
                  !erro &&
                  visibleAppointments.length === 0 && (
                    <tr>
                      <td colSpan={5} className="px-6 py-14 text-center">
                        <CalendarDays
                          size={32}
                          className="mx-auto mb-3 text-slate-300"
                        />
                        <p className="text-sm font-semibold text-slate-600">
                          Nenhum agendamento encontrado
                        </p>
                        <p className="mt-1 text-xs text-slate-400">
                          Experimente alterar o nome, mês ou ano.
                        </p>
                      </td>
                    </tr>
                  )}
              </tbody>
            </table>
          </div>

          <div className="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p className="text-xs text-slate-500">
              Página {page} de {totalPages}
            </p>

            <div className="flex items-center gap-2">
              <button
                type="button"
                disabled={page === 1}
                onClick={() => setPage((current) => current - 1)}
                className="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                <ChevronLeft size={15} />
                Anterior
              </button>

              <button
                type="button"
                disabled={page === totalPages}
                onClick={() => setPage((current) => current + 1)}
                className="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                Próxima
                <ChevronRight size={15} />
              </button>
            </div>
          </div>
        </section>

        <div className="rounded-xl border border-amber-100 bg-amber-50 p-4">
          <p className="text-sm font-semibold text-amber-900">
            Dados demonstrativos
          </p>
          <p className="mt-1 text-xs leading-relaxed text-amber-800">
            Este projeto utiliza dados fictícios armazenados em banco de
            dados para fins de demonstração. As operações de cadastro,
            consulta, edição e exclusão estão disponíveis nesta versão
            demonstrativa.
          </p>
        </div>
      </div>
    </AppShell>
  );
}
