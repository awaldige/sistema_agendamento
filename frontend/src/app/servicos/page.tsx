
"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import {
  BriefcaseBusiness,
  Plus,
  Search,
  Pencil,
  Trash2,
  X,
  Save,
  CalendarDays,
  FileText,
  LoaderCircle,
  RefreshCw,
  Clock3,
} from "lucide-react";
import { AppShell } from "@/components/layout/app-shell";

interface Servico {
  id: number;
  nome: string;
  descricao: string | null;
  duracaoMinutos: number;
  createdAt: string;
}

export default function ServicosPage() {
  const [servicos, setServicos] = useState<Servico[]>([]);
  const [busca, setBusca] = useState("");
  const [modalAberto, setModalAberto] = useState(false);
  const [servicoEditando, setServicoEditando] = useState<number | null>(null);
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");
  const [duracaoMinutos, setDuracaoMinutos] = useState("60");
  const [mensagem, setMensagem] = useState("");
  const [erro, setErro] = useState("");
  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);
  const [excluindo, setExcluindo] = useState<number | null>(null);

  const carregarServicos = useCallback(async () => {
    setCarregando(true);
    setErro("");

    try {
      const resposta = await fetch("/api/servicos", {
        cache: "no-store",
      });

      const dados = await resposta.json();

      if (!resposta.ok) {
        throw new Error(dados.erro || "Não foi possível carregar os serviços.");
      }

      setServicos(dados);
    } catch (error) {
      setErro(
        error instanceof Error
          ? error.message
          : "Erro ao conectar com o servidor."
      );
    } finally {
      setCarregando(false);
    }
  }, []);

  useEffect(() => {
    carregarServicos();
  }, [carregarServicos]);

  const servicosFiltrados = useMemo(() => {
    return servicos
      .filter((servico) =>
        `${servico.nome} ${servico.descricao || ""}`
          .toLowerCase()
          .includes(busca.toLowerCase())
      )
      .sort((a, b) => a.nome.localeCompare(b.nome, "pt-BR"));
  }, [servicos, busca]);

  function abrirNovoServico() {
    setServicoEditando(null);
    setNome("");
    setDescricao("");
    setDuracaoMinutos("60");
    setMensagem("");
    setModalAberto(true);
  }

  function abrirEdicao(servico: Servico) {
    setServicoEditando(servico.id);
    setNome(servico.nome);
    setDescricao(servico.descricao || "");
    setDuracaoMinutos(String(servico.duracaoMinutos));
    setMensagem("");
    setModalAberto(true);
  }

  async function salvarServico(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();

    const nomeLimpo = nome.trim();
    const descricaoLimpa = descricao.trim();
    const duracaoNumerica = Number(duracaoMinutos);

    if (!nomeLimpo) {
      setMensagem("Informe o nome do serviço.");
      return;
    }

    if (
      !Number.isInteger(duracaoNumerica) ||
      duracaoNumerica < 5 ||
      duracaoNumerica > 480
    ) {
      setMensagem("Informe uma duração inteira entre 5 e 480 minutos.");
      return;
    }

    setSalvando(true);
    setMensagem("");

    try {
      const editando = servicoEditando !== null;

      const resposta = await fetch(
        editando ? `/api/servicos/${servicoEditando}` : "/api/servicos",
        {
          method: editando ? "PUT" : "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            nome: nomeLimpo,
            descricao: descricaoLimpa,
            duracaoMinutos: duracaoNumerica,
          }),
        }
      );

      const dados = await resposta.json();

      if (!resposta.ok) {
        throw new Error(dados.erro || "Não foi possível salvar o serviço.");
      }

      await carregarServicos();
      setModalAberto(false);
      setNome("");
      setDescricao("");
      setDuracaoMinutos("60");
      setServicoEditando(null);
    } catch (error) {
      setMensagem(
        error instanceof Error
          ? error.message
          : "Erro ao salvar o serviço."
      );
    } finally {
      setSalvando(false);
    }
  }

  async function excluirServico(id: number) {
    const confirmar = window.confirm(
      "Deseja realmente excluir este serviço?"
    );

    if (!confirmar) return;

    setExcluindo(id);
    setErro("");

    try {
      const resposta = await fetch(`/api/servicos/${id}`, {
        method: "DELETE",
      });

      const dados = await resposta.json();

      if (!resposta.ok) {
        throw new Error(dados.erro || "Não foi possível excluir o serviço.");
      }

      await carregarServicos();
    } catch (error) {
      setErro(
        error instanceof Error
          ? error.message
          : "Erro ao excluir o serviço."
      );
    } finally {
      setExcluindo(null);
    }
  }

  function formatarData(data: string) {
    return new Intl.DateTimeFormat("pt-BR").format(new Date(data));
  }

  const inputClass =
    "w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10";

  return (
    <AppShell>
      <div className="space-y-6">
        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div>
            <h1 className="text-2xl font-bold text-slate-800">
              Serviços
            </h1>
            <p className="mt-1 text-sm text-slate-500">
              Gerencie os serviços disponíveis para agendamento.
            </p>
          </div>

          <button
            type="button"
            onClick={abrirNovoServico}
            className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
          >
            <Plus size={18} />
            Novo serviço
          </button>
        </div>

        <div className="grid gap-4 sm:grid-cols-2">
          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-slate-500">Total de serviços</p>
                <p className="mt-2 text-3xl font-bold text-slate-800">
                  {servicos.length}
                </p>
              </div>
              <div className="rounded-xl bg-blue-50 p-3 text-blue-600">
                <BriefcaseBusiness size={23} />
              </div>
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-slate-500">Resultados da busca</p>
                <p className="mt-2 text-3xl font-bold text-slate-800">
                  {servicosFiltrados.length}
                </p>
              </div>
              <div className="rounded-xl bg-indigo-50 p-3 text-indigo-600">
                <Search size={23} />
              </div>
            </div>
          </div>
        </div>

        <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div className="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div className="relative w-full max-w-md">
              <Search
                size={18}
                className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
              />
              <input
                type="search"
                value={busca}
                onChange={(e) => setBusca(e.target.value)}
                placeholder="Buscar serviço..."
                className={`${inputClass} pl-11`}
              />
            </div>

            <button
              type="button"
              onClick={carregarServicos}
              disabled={carregando}
              className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-50"
            >
              <RefreshCw size={16} className={carregando ? "animate-spin" : ""} />
              Atualizar
            </button>
          </div>

          {erro && (
            <div className="m-5 flex flex-col gap-3 rounded-xl border border-red-200 bg-red-50 p-4 sm:flex-row sm:items-center sm:justify-between">
              <p role="alert" className="text-sm text-red-700">
                {erro}
              </p>
              <button
                type="button"
                onClick={carregarServicos}
                className="shrink-0 text-sm font-semibold text-red-700 underline"
              >
                Tentar novamente
              </button>
            </div>
          )}

          {carregando ? (
            <div className="flex flex-col items-center justify-center px-5 py-16">
              <LoaderCircle size={34} className="animate-spin text-blue-600" />
              <p className="mt-4 text-sm text-slate-500">
                Carregando serviços do banco de dados...
              </p>
            </div>
          ) : servicosFiltrados.length === 0 ? (
            <div className="px-5 py-16 text-center">
              <BriefcaseBusiness
                size={36}
                className="mx-auto text-slate-300"
              />
              <h3 className="mt-4 font-semibold text-slate-700">
                Nenhum serviço encontrado
              </h3>
              <p className="mt-1 text-sm text-slate-500">
                {busca
                  ? "Tente outra pesquisa ou cadastre um novo serviço."
                  : "Cadastre o primeiro serviço para começar."}
              </p>
            </div>
          ) : (
            <>
              <div className="hidden overflow-x-auto md:block">
                <table className="w-full text-left">
                  <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                      <th className="px-6 py-4 font-semibold">ID</th>
                      <th className="px-6 py-4 font-semibold">Nome</th>
                      <th className="px-6 py-4 font-semibold">Descrição</th>
                      <th className="px-6 py-4 font-semibold">Duração</th>
                      <th className="px-6 py-4 font-semibold">Criado em</th>
                      <th className="px-6 py-4 text-right font-semibold">Ações</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {servicosFiltrados.map((servico) => (
                      <tr key={servico.id} className="transition hover:bg-slate-50/70">
                        <td className="px-6 py-4 text-sm text-slate-500">
                          #{servico.id}
                        </td>
                        <td className="px-6 py-4">
                          <span className="font-semibold text-slate-800">
                            {servico.nome}
                          </span>
                        </td>
                        <td className="max-w-xs px-6 py-4 text-sm text-slate-500">
                          {servico.descricao || "Sem descrição"}
                        </td>
                        <td className="px-6 py-4 text-sm text-slate-600">
                          <span className="inline-flex items-center gap-1.5 whitespace-nowrap">
                            <Clock3 size={15} className="text-slate-400" />
                            {servico.duracaoMinutos} min
                          </span>
                        </td>
                        <td className="px-6 py-4 text-sm text-slate-500">
                          {formatarData(servico.createdAt)}
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex justify-end gap-2">
                            <button
                              type="button"
                              onClick={() => abrirEdicao(servico)}
                              aria-label={`Editar ${servico.nome}`}
                              className="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50"
                            >
                              <Pencil size={17} />
                            </button>
                            <button
                              type="button"
                              onClick={() => excluirServico(servico.id)}
                              disabled={excluindo === servico.id}
                              aria-label={`Excluir ${servico.nome}`}
                              className="rounded-lg p-2 text-red-500 transition hover:bg-red-50 disabled:opacity-40"
                            >
                              {excluindo === servico.id ? (
                                <LoaderCircle size={17} className="animate-spin" />
                              ) : (
                                <Trash2 size={17} />
                              )}
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              <div className="space-y-3 p-4 md:hidden">
                {servicosFiltrados.map((servico) => (
                  <article
                    key={servico.id}
                    className="rounded-xl border border-slate-200 p-4"
                  >
                    <div className="flex items-start justify-between gap-3">
                      <div className="min-w-0">
                        <p className="font-semibold text-slate-800">
                          {servico.nome}
                        </p>
                        <p className="mt-1 text-sm text-slate-500">
                          {servico.descricao || "Sem descrição"}
                        </p>
                        <p className="mt-2 flex items-center gap-1.5 text-sm font-medium text-blue-700">
                          <Clock3 size={15} />
                          {servico.duracaoMinutos} minutos
                        </p>
                      </div>
                      <span className="shrink-0 text-xs text-slate-400">
                        #{servico.id}
                      </span>
                    </div>

                    <div className="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                      <span className="flex items-center gap-1.5 text-xs text-slate-500">
                        <CalendarDays size={14} />
                        {formatarData(servico.createdAt)}
                      </span>
                      <div className="flex gap-2">
                        <button
                          type="button"
                          onClick={() => abrirEdicao(servico)}
                          aria-label={`Editar ${servico.nome}`}
                          className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                        >
                          <Pencil size={17} />
                        </button>
                        <button
                          type="button"
                          onClick={() => excluirServico(servico.id)}
                          disabled={excluindo === servico.id}
                          aria-label={`Excluir ${servico.nome}`}
                          className="rounded-lg p-2 text-red-500 hover:bg-red-50 disabled:opacity-40"
                        >
                          {excluindo === servico.id ? (
                            <LoaderCircle size={17} className="animate-spin" />
                          ) : (
                            <Trash2 size={17} />
                          )}
                        </button>
                      </div>
                    </div>
                  </article>
                ))}
              </div>
            </>
          )}

          <div className="border-t border-slate-100 px-5 py-4 text-xs text-slate-500">
            Exibindo {servicosFiltrados.length} de {servicos.length} serviços
          </div>
        </section>

        <div className="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
          <FileText size={19} className="mt-0.5 shrink-0 text-blue-600" />
          <p className="text-xs leading-relaxed text-blue-800">
            Ambiente demonstrativo da AW TECHNOLOGY. Os serviços são fictícios
            e armazenados no banco de dados Neon deste projeto de portfólio.
          </p>
        </div>
      </div>

      {modalAberto && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4">
          <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-titulo"
            className="my-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl"
          >
            <div className="mb-6 flex items-center justify-between">
              <div>
                <h2 id="modal-titulo" className="text-xl font-bold text-slate-800">
                  {servicoEditando !== null ? "Editar serviço" : "Novo serviço"}
                </h2>
                <p className="mt-1 text-sm text-slate-500">
                  Informe os dados do serviço.
                </p>
              </div>
              <button
                type="button"
                onClick={() => setModalAberto(false)}
                aria-label="Fechar formulário"
                className="rounded-lg p-2 text-slate-400 hover:bg-slate-100"
              >
                <X size={20} />
              </button>
            </div>

            <form onSubmit={salvarServico} className="space-y-5">
              <div>
                <label htmlFor="nome-servico" className="mb-2 block text-sm font-semibold text-slate-700">
                  Nome do serviço <span className="text-red-500">*</span>
                </label>
                <input
                  id="nome-servico"
                  required
                  maxLength={120}
                  autoFocus
                  value={nome}
                  onChange={(e) => setNome(e.target.value)}
                  placeholder="Ex.: Consulta inicial"
                  className={inputClass}
                />
              </div>

              <div>
                <label htmlFor="duracao-servico" className="mb-2 block text-sm font-semibold text-slate-700">
                  Duração (minutos) <span className="text-red-500">*</span>
                </label>
                <input
                  id="duracao-servico"
                  type="number"
                  required
                  min={5}
                  max={480}
                  step={1}
                  value={duracaoMinutos}
                  onChange={(e) => setDuracaoMinutos(e.target.value)}
                  className={inputClass}
                />
                <p className="mt-1.5 text-xs text-slate-500">
                  Informe um valor entre 5 e 480 minutos.
                </p>
              </div>

              <div>
                <label htmlFor="descricao-servico" className="mb-2 block text-sm font-semibold text-slate-700">
                  Descrição
                </label>
                <textarea
                  id="descricao-servico"
                  rows={4}
                  maxLength={1000}
                  value={descricao}
                  onChange={(e) => setDescricao(e.target.value)}
                  placeholder="Descreva o serviço (opcional)"
                  className={`${inputClass} resize-y`}
                />
              </div>

              {mensagem && (
                <p role="alert" className="text-sm text-red-600">
                  {mensagem}
                </p>
              )}

              <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                  type="button"
                  onClick={() => setModalAberto(false)}
                  disabled={salvando}
                  className="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  disabled={salvando}
                  className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                  {salvando ? (
                    <LoaderCircle size={17} className="animate-spin" />
                  ) : (
                    <Save size={17} />
                  )}
                  {salvando ? "Salvando..." : "Salvar serviço"}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AppShell>
  );
}
