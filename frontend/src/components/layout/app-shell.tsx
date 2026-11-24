
"use client";

import { useState } from "react";
import { Sidebar } from "./sidebar";
import { Topbar } from "./topbar";

interface AppShellProps {
  children: React.ReactNode;
}

export function AppShell({ children }: AppShellProps) {
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <div className="min-h-screen w-full max-w-full overflow-x-clip bg-[#f4f6fa]">
      <Sidebar
        isOpen={menuOpen}
        onClose={() => setMenuOpen(false)}
      />

      <div className="min-h-screen w-full min-w-0 lg:pl-64">
        <Topbar onMenuClick={() => setMenuOpen(true)} />

        <main className="min-h-[calc(100vh-5rem)] w-full min-w-0 p-4 sm:p-6 lg:p-8">
          {children}
        </main>

        <footer className="border-t border-slate-200 px-4 py-5 text-center text-xs text-slate-500 sm:px-6">
          AgendaPro — Projeto demonstrativo desenvolvido por AW TECHNOLOGY.
        </footer>
      </div>
    </div>
  );
}
