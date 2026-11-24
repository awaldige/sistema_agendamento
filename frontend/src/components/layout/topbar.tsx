
"use client";

import {
  Bell,
  Menu,
  Search,
  ChevronDown,
} from "lucide-react";

interface TopbarProps {
  onMenuClick: () => void;
}

export function Topbar({ onMenuClick }: TopbarProps) {
  return (
    <header className="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
      <div className="flex items-center gap-4">
        <button
          type="button"
          onClick={onMenuClick}
          aria-label="Abrir menu"
          className="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
        >
          <Menu size={23} />
        </button>

        <div>
          <h2 className="text-lg font-bold text-slate-800">
            Painel administrativo
          </h2>
          <p className="hidden text-xs text-slate-500 sm:block">
            Gerencie seus agendamentos em um só lugar
          </p>
        </div>
      </div>

      <div className="flex items-center gap-3 sm:gap-5">
        <button
          type="button"
          aria-label="Pesquisar"
          className="hidden rounded-full p-2.5 text-slate-500 hover:bg-slate-100 sm:block"
        >
          <Search size={20} />
        </button>

        <button
          type="button"
          aria-label="Notificações"
          className="relative rounded-full p-2.5 text-slate-500 hover:bg-slate-100"
        >
          <Bell size={20} />
          <span className="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white" />
        </button>

        <div className="h-8 w-px bg-slate-200" />

        <button
          type="button"
          className="flex items-center gap-2"
          aria-label="Perfil demonstrativo"
        >
          <div className="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
            AD
          </div>

          <div className="hidden text-left sm:block">
            <p className="text-sm font-semibold text-slate-700">
              Administrador
            </p>
            <p className="text-xs text-slate-500">Demonstração</p>
          </div>

          <ChevronDown size={16} className="hidden text-slate-400 sm:block" />
        </button>
      </div>
    </header>
  );
}

