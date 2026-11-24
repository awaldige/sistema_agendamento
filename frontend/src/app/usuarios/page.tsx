"use client";

import { useMemo, useState } from "react";
import {
  Users,
  UserPlus,
  Search,
  Pencil,
  Trash2,
  X,
  Save,
  ShieldCheck,
  BriefcaseBusiness,
  Headset,
  UserCheck,
  UserX,
  Mail,
  FileText,
} from "lucide-react";
import { AppShell } from "@/components/layout/app-shell";

type Perfil = "Administrador" | "Profissional" | "Recepção";
type Status = "Ativo" | "Inativo";

interface Usuario {
  id: number;
  nome: string;
  email: string;
  perfil: Perfil;
  status: Status;
  createdAt: string;
}

const usuariosIniciais: Usuario[] = [
  {
    id: 1,
    nome: "Ana Martins",
    email: "ana.martins@example.com",
    perfil: "Administrador",
    status: "Ativo",
    createdAt: "2026-10-10",
  },
  {
    id: 2,
    nome: "Carlos Oliveira",
    email: "carlos.oliveira@example.com",
    perfil: "Profissional",
    status: "Ativo",
    createdAt: "2026-10-15",
  },
  {
    id: 3,
    nome: "Juliana Costa",
    email: "juliana.costa@example.com",
    perfil: "Recepção",
    status: "Ativo",
    createdAt: "2026-10-22",
  },
  {
    id: 4,
    nome: "Rafael Santos",
    email: "rafael.santos@example.com",
    perfil: "Profissional",
    status: "Inativo",
    createdAt: "2026-11-02",
  },
];

export default function UsuariosPage() {
  const [usuarios, setUsuarios] = useState(usuariosIniciais);
  const [busca, setBusca] = useState("");
  const [filtroPerfil, setFiltroPerfil] = useState("Todos");
  const [filtroStatus, setFiltroStatus] = useState("Todos");
  const [modalAberto, setModalAberto] = useState(false);
  const [usuarioEditando, setUsuarioEditando] = useState<number | null>(null);

  const [nome, setNome] = useState("");
  const [email, setEmail] = useState("");
  const [perfil, setPerfil] = useState<Perfil>("Profissional");
  const [mensagem, setMensagem] = useState("");

  const usuariosFiltrados = useMemo(() => {
    return usuarios
      .filter((usuario) =>
        `${usuario.nome} ${usuario.email}`
          .toLowerCase()
          .includes(busca.toLowerCase())
      )
      .filter(
        (usuario) =>
          filtroPerfil === "Todos" || usuario.perfil === filtroPerfil
      )
      .filter(
        (usuario) =>
          filtroStatus === "Todos" || usuario.status === filtroStatus
      )
      .sort((a, b) => a.nome.localeCompare(b.nome, "pt-BR"));
  }, [usuarios, busca, filtroPerfil, filtroStatus]);

  const totalAtivos = usuarios.filter(
    (usuario) => usuario.status === "Ativo"
  ).length;

  const totalInativos = usuarios.filter(
    (usuario) => usuario.status === "Inativo"
  ).length;

  function abrirNovoUsuario() {
    setUsuarioEditando(null);
    setNome("");
    setEmail("");
    setPerfil("Profissional");
    setMensagem("");
    setModalAberto(true);
  }

  function abrirEdicao(usuario: Usuario) {
    setUsuarioEditando(usuario.id);
    setNome(usuario.nome);
    setEmail(usuario.email);
    setPerfil(usuario.perfil);
    setMensagem("");
    setModalAberto(true);
  }

  function salvarUsuario(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();

    const nomeLimpo = nome.trim();
    const emailLimpo = email.trim().toLowerCase();

    if (!nomeLimpo || !emailLimpo) {
      setMensagem("Preencha todos os campos obrigatórios.");
      return;
    }

    const emailDuplicado = usuarios.some(
      (usuario) =>
        usuario.email.toLowerCase() === emailLimpo &&
        usuario.id !== usuarioEditando
    );

    if (emailDuplicado) {
      setMensagem("Já existe um usuário cadastrado com este e-mail.");
      return;
    }

    if (usuarioEditando !== null) {
      setUsuarios((atuais) =>
        atuais.map((usuario) =>
          usuario.id === usuarioEditando
            ? { ...usuario, nome: nomeLimpo, email: emailLimpo, perfil }
            : usuario
        )
      );
    } else {
      const novoUsuario: Usuario = {
        id: Math.max(0, ...usuarios.map((usuario) => usuario.id)) + 1,
        nome: nomeLimpo,
        email: emailLimpo,
        perfil,
        status: "Ativo",
        createdAt: new Date().toISOString().slice(0, 10),
      };

      setUsuarios((atuais) => [...atuais, novoUsuario]);
    }

    setModalAberto(false);
    setMensagem("");
  }

  function alternarStatus(id: number) {
    setUsuarios((atuais) =>
      atuais.map((usuario) =>
        usuario.id === id
          ? {
              ...usuario,
              status: usuario.status === "Ativo" ? "Inativo" : "Ativo",
            }
          : usuario
      )
    );
  }

  function excluirUsuario(id: number) {
    const confirmar = window.confirm(
      "Deseja realmente excluir este usuário?"
    );

    if (!confirmar) return;

    setUsuarios((atuais) =>
      atuais.filter((usuario) => usuario.id !== id)
    );
  }

  function formatarData(data: string) {
    return new Intl.DateTimeFormat("pt-BR").format(
      new Date(`${data}T12:00:00`)
    );
  }

  function classePerfil(perfil: Perfil) {
    if (perfil === "Administrador") {
      return "bg-purple-50 text-purple-700";
    }

    if (perfil === "Profissional") {
      return "bg-blue-50 text-blue-700";
    }

    return "bg-amber-50 text-amber-700";
  }

  const inputClass =
    "w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10";

  return (
    <AppShell>
      <div className="mx-auto max-w-7xl space-y-6">
        <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
              Usuários
            </h1>
            <p className="mt-1 text-sm text-slate-500">
              Gerencie os usuários e seus perfis de acesso.
            </p>
          </div>

          <button
            type="button"
            onClick={abrirNovoUsuario}
            className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
          >
            <UserPlus size={18} />
            Novo usuário
          </button>
        </section>

        <section className="grid gap-4 sm:grid-cols-3">
          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-slate-500">Total de usuários</p>
                <p className="mt-2 text-3xl font-bold text-slate-800">
                  {usuarios.length}
                </p>
              </div>
              <div className="rounded-xl bg-blue-50 p-3 text-blue-600">
                <Users size={23} />
              </div>
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-slate-500">Usuários ativos</p>
                <p className="mt-2 text-3xl font-bold text-slate-800">
                  {totalAtivos}
                </p>
              </div>
              <div className="rounded-xl bg-green-50 p-3 text-green-600">
                <UserCheck size={23} />
              </div>
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-slate-500">Usuários inativos</p>
                <p className="mt-2 text-3xl font-bold text-slate-800">
                  {totalInativos}
                </p>
              </div>
              <div className="rounded-xl bg-red-50 p-3 text-red-500">
                <UserX size={23} />
              </div>
            </div>
          </div>
        </section>

        <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div className="flex flex-col gap-4 border-b border-slate-100 p-5 lg:flex-row">
            <div className="relative flex-1">
              <Search
                size={18}
                className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
              />
              <input
                type="search"
                value={busca}
                onChange={(e) => setBusca(e.target.value)}
                placeholder="Buscar por nome ou e-mail..."
                className={`${inputClass} pl-11`}
              />
            </div>

            <select
              value={filtroPerfil}
              onChange={(e) => setFiltroPerfil(e.target.value)}
              className={`${inputClass} lg:max-w-52`}
            >
              <option value="Todos">Todos os perfis</option>
              <option value="Administrador">Administrador</option>
              <option value="Profissional">Profissional</option>
              <option value="Recepção">Recepção</option>
            </select>

            <select
              value={filtroStatus}
              onChange={(e) => setFiltroStatus(e.target.value)}
              className={`${inputClass} lg:max-w-44`}
            >
              <option value="Todos">Todas as situações</option>
              <option value="Ativo">Ativos</option>
              <option value="Inativo">Inativos</option>
            </select>
          </div>

          {usuariosFiltrados.length === 0 ? (
            <div className="px-5 py-16 text-center">
              <Users size={36} className="mx-auto text-slate-300" />
              <h3 className="mt-4 font-semibold text-slate-700">
                Nenhum usuário encontrado
              </h3>
              <p className="mt-1 text-sm text-slate-500">
                Tente alterar os filtros ou cadastre um novo usuário.
              </p>
            </div>
          ) : (
            <>
              <div className="hidden overflow-x-auto md:block">
                <table className="w-full text-left">
                  <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                      <th className="px-6 py-4 font-semibold">Usuário</th>
                      <th className="px-6 py-4 font-semibold">Perfil</th>
                      <th className="px-6 py-4 font-semibold">Criado em</th>
                      <th className="px-6 py-4 font-semibold">Situação</th>
                      <th className="px-6 py-4 text-right font-semibold">
                        Ações
                      </th>
                    </tr>
                  </thead>

                  <tbody className="divide-y divide-slate-100">
                    {usuariosFiltrados.map((usuario) => (
                      <tr
                        key={usuario.id}
                        className="transition hover:bg-slate-50/70"
                      >
                        <td className="px-6 py-4">
                          <p className="font-semibold text-slate-800">
                            {usuario.nome}
                          </p>
                          <p className="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                            <Mail size={14} />
                            {usuario.email}
                          </p>
                        </td>

                        <td className="px-6 py-4">
                          <span
                            className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${classePerfil(usuario.perfil)}`}
                          >
                            {usuario.perfil}
                          </span>
                        </td>

                        <td className="px-6 py-4 text-sm text-slate-500">
                          {formatarData(usuario.createdAt)}
                        </td>

                        <td className="px-6 py-4">
                          <span
                            className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ${
                              usuario.status === "Ativo"
                                ? "bg-green-50 text-green-700"
                                : "bg-slate-100 text-slate-500"
                            }`}
                          >
                            <span
                              className={`h-1.5 w-1.5 rounded-full ${
                                usuario.status === "Ativo"
                                  ? "bg-green-500"
                                  : "bg-slate-400"
                              }`}
                            />
                            {usuario.status}
                          </span>
                        </td>

                        <td className="px-6 py-4">
                          <div className="flex justify-end gap-2">
                            <button
                              type="button"
                              onClick={() => alternarStatus(usuario.id)}
                              title={
                                usuario.status === "Ativo"
                                  ? "Desativar usuário"
                                  : "Ativar usuário"
                              }
                              className="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100"
                            >
                              {usuario.status === "Ativo" ? (
                                <UserX size={17} />
                              ) : (
                                <UserCheck size={17} />
                              )}
                            </button>

                            <button
                              type="button"
                              onClick={() => abrirEdicao(usuario)}
                              aria-label={`Editar ${usuario.nome}`}
                              className="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50"
                            >
                              <Pencil size={17} />
                            </button>

                            <button
                              type="button"
                              onClick={() => excluirUsuario(usuario.id)}
                              aria-label={`Excluir ${usuario.nome}`}
                              className="rounded-lg p-2 text-red-500 transition hover:bg-red-50"
                            >
                              <Trash2 size={17} />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              <div className="space-y-3 p-4 md:hidden">
                {usuariosFiltrados.map((usuario) => (
                  <article
                    key={usuario.id}
                    className="rounded-xl border border-slate-200 p-4"
                  >
                    <div className="flex items-start justify-between gap-3">
                      <div className="min-w-0">
                        <p className="font-semibold text-slate-800">
                          {usuario.nome}
                        </p>
                        <p className="mt-1 break-all text-sm text-slate-500">
                          {usuario.email}
                        </p>
                      </div>

                      <span
                        className={`shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ${classePerfil(usuario.perfil)}`}
                      >
                        {usuario.perfil}
                      </span>
                    </div>

                    <div className="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                      <span
                        className={`text-xs font-semibold ${
                          usuario.status === "Ativo"
                            ? "text-green-600"
                            : "text-slate-500"
                        }`}
                      >
                        {usuario.status}
                      </span>

                      <div className="flex gap-2">
                        <button
                          type="button"
                          onClick={() => alternarStatus(usuario.id)}
                          aria-label="Alterar situação"
                          className="rounded-lg p-2 text-slate-500 hover:bg-slate-100"
                        >
                          {usuario.status === "Ativo" ? (
                            <UserX size={17} />
                          ) : (
                            <UserCheck size={17} />
                          )}
                        </button>

                        <button
                          type="button"
                          onClick={() => abrirEdicao(usuario)}
                          aria-label={`Editar ${usuario.nome}`}
                          className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                        >
                          <Pencil size={17} />
                        </button>

                        <button
                          type="button"
                          onClick={() => excluirUsuario(usuario.id)}
                          aria-label={`Excluir ${usuario.nome}`}
                          className="rounded-lg p-2 text-red-500 hover:bg-red-50"
                        >
                          <Trash2 size={17} />
                        </button>
                      </div>
                    </div>
                  </article>
                ))}
              </div>
            </>
          )}

          <div className="border-t border-slate-100 px-5 py-4 text-xs text-slate-500">
            Exibindo {usuariosFiltrados.length} de {usuarios.length} usuários
          </div>
        </section>

        <div className="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
          <FileText size={19} className="mt-0.5 shrink-0 text-blue-600" />
          <p className="text-xs leading-relaxed text-blue-800">
            Ambiente demonstrativo: os usuários e seus dados são fictícios.
            As alterações ficam apenas na memória da página. A autenticação,
            as permissões reais e a integração com o banco serão implementadas
            posteriormente.
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
                <h2
                  id="modal-titulo"
                  className="text-xl font-bold text-slate-800"
                >
                  {usuarioEditando !== null
                    ? "Editar usuário"
                    : "Novo usuário"}
                </h2>
                <p className="mt-1 text-sm text-slate-500">
                  Informe os dados do usuário.
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

            <form onSubmit={salvarUsuario} className="space-y-5">
              <div>
                <label
                  htmlFor="nome-usuario"
                  className="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Nome completo <span className="text-red-500">*</span>
                </label>
                <input
                  id="nome-usuario"
                  required
                  maxLength={120}
                  autoFocus
                  value={nome}
                  onChange={(e) => setNome(e.target.value)}
                  placeholder="Ex.: Maria Oliveira"
                  className={inputClass}
                />
              </div>

              <div>
                <label
                  htmlFor="email-usuario"
                  className="mb-2 block text-sm font-semibold text-slate-700"
                >
                  E-mail <span className="text-red-500">*</span>
                </label>
                <input
                  id="email-usuario"
                  type="email"
                  required
                  maxLength={180}
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="usuario@example.com"
                  className={inputClass}
                />
              </div>

              <div>
                <label
                  htmlFor="perfil-usuario"
                  className="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Perfil de acesso <span className="text-red-500">*</span>
                </label>

                <select
                  id="perfil-usuario"
                  value={perfil}
                  onChange={(e) => setPerfil(e.target.value as Perfil)}
                  className={inputClass}
                >
                  <option value="Administrador">Administrador</option>
                  <option value="Profissional">Profissional</option>
                  <option value="Recepção">Recepção</option>
                </select>
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
                  className="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                >
                  Cancelar
                </button>

                <button
                  type="submit"
                  className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                >
                  <Save size={17} />
                  Salvar usuário
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AppShell>
  );
}
