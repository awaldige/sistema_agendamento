"use client";

import { useState } from "react";
import {
  Building2,
  Clock3,
  CalendarClock,
  Bell,
  Save,
  CheckCircle2,
  Settings2,
} from "lucide-react";
import { AppShell } from "@/components/layout/app-shell";

const diasSemana = [
  "Segunda-feira",
  "Terça-feira",
  "Quarta-feira",
  "Quinta-feira",
  "Sexta-feira",
  "Sábado",
  "Domingo",
];

export default function ConfiguracoesPage() {
  const [empresa, setEmpresa] = useState({
    nome: "Clínica Demonstrativa",
    email: "contato@example.com",
    telefone: "(11) 99999-0000",
    endereco: "Rua Exemplo, 100",
    cidade: "São Paulo",
  });

  const [diasAtendimento, setDiasAtendimento] = useState([
    "Segunda-feira",
    "Terça-feira",
    "Quarta-feira",
    "Quinta-feira",
    "Sexta-feira",
  ]);

  const [horarioInicio, setHorarioInicio] = useState("08:00");
  const [horarioFim, setHorarioFim] = useState("18:00");
  const [duracao, setDuracao] = useState("30");
  const [intervalo, setIntervalo] = useState("10");
  const [antecedencia, setAntecedencia] = useState("1");

  const [notificacaoEmail, setNotificacaoEmail] = useState(true);
  const [notificacaoAgendamento, setNotificacaoAgendamento] =
    useState(true);
  const [notificacaoCancelamento, setNotificacaoCancelamento] =
    useState(false);

  const [mensagem, setMensagem] = useState("");

  function alternarDia(dia: string) {
    setDiasAtendimento((atuais) =>
      atuais.includes(dia)
        ? atuais.filter((item) => item !== dia)
        : [...atuais, dia]
    );
  }

  function salvarConfiguracoes(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setMensagem("Configurações atualizadas com sucesso nesta demonstração.");

    window.setTimeout(() => {
      setMensagem("");
    }, 4000);
  }

  const inputClass =
    "w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10";

  const secaoClass =
    "overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm";

  return (
    <AppShell>
      <div className="mx-auto max-w-7xl space-y-6">
        <section className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
              Configurações
            </h1>
            <p className="mt-1 text-sm text-slate-500">
              Personalize as informações e o funcionamento do seu sistema.
            </p>
          </div>

          <div className="flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700">
            <Settings2 size={18} />
            Preferências do sistema
          </div>
        </section>

        <form onSubmit={salvarConfiguracoes} className="space-y-6">
          <section className={secaoClass}>
            <div className="flex items-center gap-3 border-b border-slate-100 p-5">
              <div className="rounded-xl bg-blue-50 p-3 text-blue-600">
                <Building2 size={22} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Dados da empresa
                </h2>
                <p className="mt-1 text-xs text-slate-500">
                  Informações gerais do estabelecimento.
                </p>
              </div>
            </div>

            <div className="grid gap-5 p-5 sm:grid-cols-2">
              <div className="sm:col-span-2">
                <label htmlFor="empresa-nome" className="mb-2 block text-sm font-semibold text-slate-700">
                  Nome da empresa
                </label>
                <input
                  id="empresa-nome"
                  required
                  maxLength={120}
                  value={empresa.nome}
                  onChange={(e) =>
                    setEmpresa({ ...empresa, nome: e.target.value })
                  }
                  className={inputClass}
                />
              </div>

              <div>
                <label htmlFor="empresa-email" className="mb-2 block text-sm font-semibold text-slate-700">
                  E-mail
                </label>
                <input
                  id="empresa-email"
                  type="email"
                  required
                  value={empresa.email}
                  onChange={(e) =>
                    setEmpresa({ ...empresa, email: e.target.value })
                  }
                  className={inputClass}
                />
              </div>

              <div>
                <label htmlFor="empresa-telefone" className="mb-2 block text-sm font-semibold text-slate-700">
                  Telefone
                </label>
                <input
                  id="empresa-telefone"
                  value={empresa.telefone}
                  onChange={(e) =>
                    setEmpresa({ ...empresa, telefone: e.target.value })
                  }
                  className={inputClass}
                />
              </div>

              <div>
                <label htmlFor="empresa-endereco" className="mb-2 block text-sm font-semibold text-slate-700">
                  Endereço
                </label>
                <input
                  id="empresa-endereco"
                  value={empresa.endereco}
                  onChange={(e) =>
                    setEmpresa({ ...empresa, endereco: e.target.value })
                  }
                  className={inputClass}
                />
              </div>

              <div>
                <label htmlFor="empresa-cidade" className="mb-2 block text-sm font-semibold text-slate-700">
                  Cidade
                </label>
                <input
                  id="empresa-cidade"
                  value={empresa.cidade}
                  onChange={(e) =>
                    setEmpresa({ ...empresa, cidade: e.target.value })
                  }
                  className={inputClass}
                />
              </div>
            </div>
          </section>

          <section className={secaoClass}>
            <div className="flex items-center gap-3 border-b border-slate-100 p-5">
              <div className="rounded-xl bg-indigo-50 p-3 text-indigo-600">
                <Clock3 size={22} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Horários de funcionamento
                </h2>
                <p className="mt-1 text-xs text-slate-500">
                  Defina os dias e horários disponíveis para atendimento.
                </p>
              </div>
            </div>

            <div className="space-y-5 p-5">
              <div>
                <p className="mb-3 text-sm font-semibold text-slate-700">
                  Dias de atendimento
                </p>

                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                  {diasSemana.map((dia) => (
                    <label
                      key={dia}
                      className="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-50"
                    >
                      <input
                        type="checkbox"
                        checked={diasAtendimento.includes(dia)}
                        onChange={() => alternarDia(dia)}
                        className="h-4 w-4 rounded accent-blue-600"
                      />
                      {dia}
                    </label>
                  ))}
                </div>
              </div>

              <div className="grid gap-5 sm:grid-cols-2">
                <div>
                  <label htmlFor="horario-inicio" className="mb-2 block text-sm font-semibold text-slate-700">
                    Horário de abertura
                  </label>
                  <input
                    id="horario-inicio"
                    type="time"
                    value={horarioInicio}
                    onChange={(e) => setHorarioInicio(e.target.value)}
                    className={inputClass}
                  />
                </div>

                <div>
                  <label htmlFor="horario-fim" className="mb-2 block text-sm font-semibold text-slate-700">
                    Horário de encerramento
                  </label>
                  <input
                    id="horario-fim"
                    type="time"
                    value={horarioFim}
                    onChange={(e) => setHorarioFim(e.target.value)}
                    className={inputClass}
                  />
                </div>
              </div>
            </div>
          </section>

          <section className={secaoClass}>
            <div className="flex items-center gap-3 border-b border-slate-100 p-5">
              <div className="rounded-xl bg-green-50 p-3 text-green-600">
                <CalendarClock size={22} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Configurações de agendamento
                </h2>
                <p className="mt-1 text-xs text-slate-500">
                  Ajuste os parâmetros padrão dos agendamentos.
                </p>
              </div>
            </div>

            <div className="grid gap-5 p-5 sm:grid-cols-3">
              <div>
                <label htmlFor="duracao" className="mb-2 block text-sm font-semibold text-slate-700">
                  Duração padrão
                </label>
                <select
                  id="duracao"
                  value={duracao}
                  onChange={(e) => setDuracao(e.target.value)}
                  className={inputClass}
                >
                  <option value="15">15 minutos</option>
                  <option value="30">30 minutos</option>
                  <option value="45">45 minutos</option>
                  <option value="60">1 hora</option>
                  <option value="90">1 hora e 30 minutos</option>
                </select>
              </div>

              <div>
                <label htmlFor="intervalo" className="mb-2 block text-sm font-semibold text-slate-700">
                  Intervalo entre horários
                </label>
                <select
                  id="intervalo"
                  value={intervalo}
                  onChange={(e) => setIntervalo(e.target.value)}
                  className={inputClass}
                >
                  <option value="0">Sem intervalo</option>
                  <option value="5">5 minutos</option>
                  <option value="10">10 minutos</option>
                  <option value="15">15 minutos</option>
                  <option value="30">30 minutos</option>
                </select>
              </div>

              <div>
                <label htmlFor="antecedencia" className="mb-2 block text-sm font-semibold text-slate-700">
                  Antecedência mínima
                </label>
                <select
                  id="antecedencia"
                  value={antecedencia}
                  onChange={(e) => setAntecedencia(e.target.value)}
                  className={inputClass}
                >
                  <option value="0">Sem restrição</option>
                  <option value="1">1 dia</option>
                  <option value="2">2 dias</option>
                  <option value="3">3 dias</option>
                  <option value="7">7 dias</option>
                </select>
              </div>
            </div>
          </section>

          <section className={secaoClass}>
            <div className="flex items-center gap-3 border-b border-slate-100 p-5">
              <div className="rounded-xl bg-amber-50 p-3 text-amber-600">
                <Bell size={22} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Preferências e notificações
                </h2>
                <p className="mt-1 text-xs text-slate-500">
                  Configure as opções de notificação do sistema.
                </p>
              </div>
            </div>

            <div className="divide-y divide-slate-100 px-5">
              <label className="flex cursor-pointer items-center justify-between gap-4 py-4">
                <div>
                  <p className="text-sm font-semibold text-slate-700">
                    Notificações por e-mail
                  </p>
                  <p className="mt-1 text-xs text-slate-500">
                    Permitir o envio de notificações por e-mail.
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={notificacaoEmail}
                  onChange={(e) => setNotificacaoEmail(e.target.checked)}
                  className="h-5 w-5 shrink-0 accent-blue-600"
                />
              </label>

              <label className="flex cursor-pointer items-center justify-between gap-4 py-4">
                <div>
                  <p className="text-sm font-semibold text-slate-700">
                    Novos agendamentos
                  </p>
                  <p className="mt-1 text-xs text-slate-500">
                    Notificar quando um novo agendamento for registrado.
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={notificacaoAgendamento}
                  onChange={(e) =>
                    setNotificacaoAgendamento(e.target.checked)
                  }
                  className="h-5 w-5 shrink-0 accent-blue-600"
                />
              </label>

              <label className="flex cursor-pointer items-center justify-between gap-4 py-4">
                <div>
                  <p className="text-sm font-semibold text-slate-700">
                    Cancelamentos
                  </p>
                  <p className="mt-1 text-xs text-slate-500">
                    Notificar quando um agendamento for cancelado.
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={notificacaoCancelamento}
                  onChange={(e) =>
                    setNotificacaoCancelamento(e.target.checked)
                  }
                  className="h-5 w-5 shrink-0 accent-blue-600"
                />
              </label>
            </div>
          </section>

          {mensagem && (
            <div
              role="status"
              className="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700"
            >
              <CheckCircle2 size={20} />
              {mensagem}
            </div>
          )}

          <div className="flex flex-col justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50/70 p-5 sm:flex-row sm:items-center">
            <div>
              <p className="text-sm font-semibold text-blue-900">
                Ambiente demonstrativo
              </p>
              <p className="mt-1 text-xs text-blue-700">
                As configurações são ilustrativas e não são gravadas no banco
                de dados nesta etapa.
              </p>
            </div>

            <button
              type="submit"
              className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
            >
              <Save size={18} />
              Salvar configurações
            </button>
          </div>
        </form>
      </div>
    </AppShell>
  );
}
