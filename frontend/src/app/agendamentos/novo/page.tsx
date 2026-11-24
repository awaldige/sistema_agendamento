
"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import {
  ArrowLeft,
  CalendarDays,
  Clock3,
  UserRound,
  Mail,
  Phone,
  BriefcaseBusiness,
  FileText,
  Save,
} from "lucide-react";
import { useRouter } from "next/navigation";
import { AppShell } from "@/components/layout/app-shell";

interface Servico {
  id: number;
  nome: string;
}

export default function NovoAgendamentoPage() {
  const router = useRouter();

  const [paciente, setPaciente] = useState("");
  const [email, setEmail] = useState("");
  const [telefone, setTelefone] = useState("");
  const [data, setData] = useState("");
  const [hora, setHora] = useState("");
  const [servico, setServico] = useState("");
  const [tipo, setTipo] = useState("particular");
  const [observacoes, setObservacoes] = useState("");
  const [mensagem, setMensagem] = useState("");

  const [servicos, setServicos] = useState<Servico[]>([]);
  const [carregandoServicos, setCarregandoServicos] = useState(true);
  const [salvando, setSalvando] = useState(false);
  const [erro, setErro] = useState(false);

  useEffect(() => {
    async function carregarServicos() {
      try {
        const resposta = await fetch("/api/servicos");
        const dados = await resposta.json();

        if (!resposta.ok) {
          throw new Error(dados.erro || "Erro ao carregar serviços.");
        }

        setServicos(dados);
      } catch (error) {
        console.error("Erro ao carregar serviços:", error);
        setErro(true);
        setMensagem(
          "Não foi possível carregar os serviços. Tente atualizar a página."
        );
      } finally {
        setCarregandoServicos(false);
      }
    }

    carregarServicos();
  }, []);

  async function handleSubmit(
    event: React.FormEvent<HTMLFormElement>
  ) {
    event.preventDefault();

    if (salvando) return;

    setMensagem("");
    setErro(false);
    setSalvando(true);

    try {
      const resposta = await fetch("/api/agendamentos", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          paciente,
          email,
          telefone,
          data,
          hora,
          servicoId: Number(servico),
          tipo,
          observacoes,
        }),
      });

      const resultado = await resposta.json();

      if (!resposta.ok) {
        throw new Error(
          resultado.erro || "Não foi possível cadastrar o agendamento."
        );
      }

      setMensagem("Agendamento cadastrado com sucesso!");

      router.push("/agendamentos");
      router.refresh();
    } catch (error) {
      console.error("Erro ao salvar agendamento:", error);
      setErro(true);

      setMensagem(
        error instanceof Error
          ? error.message
          : "Ocorreu um erro ao salvar o agendamento."
      );
    } finally {
      setSalvando(false);
    }
  }

  const inputClass =
    "w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10";

  const labelClass =
    "mb-2 block text-sm font-semibold text-slate-700";

  return (
    <AppShell>
      <div className="mx-auto max-w-5xl space-y-6">
        <div>
          <Link
            href="/agendamentos"
            className="mb-4 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-blue-600"
          >
            <ArrowLeft size={17} />
            Voltar para agendamentos
          </Link>

          <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
              <h1 className="text-2xl font-bold text-slate-800">
                Novo agendamento
              </h1>
              <p className="mt-1 text-sm text-slate-500">
                Preencha os dados para cadastrar um novo agendamento.
              </p>
            </div>

            <span className="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
              NOVO REGISTRO
            </span>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div className="mb-6 flex items-center gap-3 border-b border-slate-100 pb-5">
              <div className="rounded-xl bg-blue-50 p-3 text-blue-600">
                <UserRound size={21} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Dados do paciente
                </h2>
                <p className="text-xs text-slate-500">
                  Informações de identificação e contato
                </p>
              </div>
            </div>

            <div className="grid gap-5 md:grid-cols-2">
              <div className="md:col-span-2">
                <label htmlFor="paciente" className={labelClass}>
                  Nome do paciente <span className="text-red-500">*</span>
                </label>
                <div className="relative">
                  <UserRound
                    size={18}
                    className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />
                  <input
                    id="paciente"
                    type="text"
                    required
                    maxLength={120}
                    value={paciente}
                    onChange={(e) => setPaciente(e.target.value)}
                    placeholder="Digite o nome completo"
                    className={`${inputClass} pl-11`}
                  />
                </div>
              </div>

              <div>
                <label htmlFor="email" className={labelClass}>
                  E-mail
                </label>
                <div className="relative">
                  <Mail
                    size={18}
                    className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />
                  <input
                    id="email"
                    type="email"
                    maxLength={150}
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="exemplo@email.com"
                    className={`${inputClass} pl-11`}
                  />
                </div>
              </div>

              <div>
                <label htmlFor="telefone" className={labelClass}>
                  Telefone
                </label>
                <div className="relative">
                  <Phone
                    size={18}
                    className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />
                  <input
                    id="telefone"
                    type="tel"
                    maxLength={20}
                    value={telefone}
                    onChange={(e) => setTelefone(e.target.value)}
                    placeholder="(00) 00000-0000"
                    className={`${inputClass} pl-11`}
                  />
                </div>
              </div>
            </div>
          </section>

          <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div className="mb-6 flex items-center gap-3 border-b border-slate-100 pb-5">
              <div className="rounded-xl bg-indigo-50 p-3 text-indigo-600">
                <CalendarDays size={21} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Informações do agendamento
                </h2>
                <p className="text-xs text-slate-500">
                  Data, horário e serviço
                </p>
              </div>
            </div>

            <div className="grid gap-5 md:grid-cols-2">
              <div>
                <label htmlFor="data" className={labelClass}>
                  Data <span className="text-red-500">*</span>
                </label>
                <div className="relative">
                  <CalendarDays
                    size={18}
                    className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />
                  <input
                    id="data"
                    type="date"
                    required
                    value={data}
                    onChange={(e) => setData(e.target.value)}
                    className={`${inputClass} pl-11`}
                  />
                </div>
              </div>

              <div>
                <label htmlFor="hora" className={labelClass}>
                  Horário <span className="text-red-500">*</span>
                </label>
                <div className="relative">
                  <Clock3
                    size={18}
                    className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />
                  <input
                    id="hora"
                    type="time"
                    required
                    value={hora}
                    onChange={(e) => setHora(e.target.value)}
                    className={`${inputClass} pl-11`}
                  />
                </div>
              </div>

              <div>
                <label htmlFor="servico" className={labelClass}>
                  Serviço <span className="text-red-500">*</span>
                </label>
                <div className="relative">
                  <BriefcaseBusiness
                    size={18}
                    className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />
                  <select
                    id="servico"
                    required
                    value={servico}
                    onChange={(e) => setServico(e.target.value)}
                    disabled={carregandoServicos || servicos.length === 0}
                    className={`${inputClass} appearance-none pl-11`}
                  >
                    <option value="">
                      {carregandoServicos
                        ? "Carregando serviços..."
                        : servicos.length === 0
                          ? "Nenhum serviço disponível"
                          : "Selecione um serviço"}
                    </option>

                    {servicos.map((item) => (
                      <option key={item.id} value={String(item.id)}>
                        {item.nome}
                      </option>
                    ))}
                  </select>
                </div>
                <p className="mt-2 text-xs text-slate-400">
                  Serviços cadastrados no sistema.
                </p>
              </div>

              <div>
                <label htmlFor="tipo" className={labelClass}>
                  Tipo de consulta <span className="text-red-500">*</span>
                </label>
                <select
                  id="tipo"
                  required
                  value={tipo}
                  onChange={(e) => setTipo(e.target.value)}
                  className={inputClass}
                >
                  <option value="particular">Particular</option>
                  <option value="convenio">Convênio</option>
                </select>
              </div>
            </div>
          </section>

          <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div className="mb-5 flex items-center gap-3">
              <div className="rounded-xl bg-amber-50 p-3 text-amber-600">
                <FileText size={21} />
              </div>
              <div>
                <h2 className="font-bold text-slate-800">
                  Observações
                </h2>
                <p className="text-xs text-slate-500">
                  Campo opcional
                </p>
              </div>
            </div>

            <textarea
              id="observacoes"
              rows={4}
              maxLength={1000}
              value={observacoes}
              onChange={(e) => setObservacoes(e.target.value)}
              placeholder="Adicione informações relevantes sobre o agendamento..."
              className={`${inputClass} resize-y`}
            />
          </section>

          {mensagem && (
            <div
              role="status"
              aria-live="polite"
              className={`rounded-xl border p-4 text-sm leading-relaxed ${
                erro
                  ? "border-red-200 bg-red-50 text-red-800"
                  : "border-blue-200 bg-blue-50 text-blue-800"
              }`}
            >
              {mensagem}
            </div>
          )}

          <div className="flex flex-col-reverse justify-end gap-3 sm:flex-row">
            <Link
              href="/agendamentos"
              className="rounded-xl border border-slate-200 bg-white px-6 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >
              Cancelar
            </Link>

            <button
              type="submit"
              disabled={salvando || carregandoServicos || servicos.length === 0}
              className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <Save size={18} />
              {salvando ? "Salvando..." : "Salvar agendamento"}
            </button>
          </div>
        </form>
      </div>
    </AppShell>
  );
}
