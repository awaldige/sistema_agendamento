
import { Clock3, MoreHorizontal } from "lucide-react";

const appointments = [
  {
    id: 1,
    name: "Mariana Oliveira",
    service: "Consulta inicial",
    time: "09:00",
    professional: "Dr. Carlos Mendes",
    status: "Confirmado",
    color: "bg-emerald-100 text-emerald-700",
    initials: "MO",
    avatar: "bg-blue-100 text-blue-700",
  },
  {
    id: 2,
    name: "Rafael Santos",
    service: "Retorno",
    time: "10:30",
    professional: "Dra. Ana Costa",
    status: "Pendente",
    color: "bg-amber-100 text-amber-700",
    initials: "RS",
    avatar: "bg-purple-100 text-purple-700",
  },
  {
    id: 3,
    name: "Juliana Ferreira",
    service: "Avaliação",
    time: "13:00",
    professional: "Dr. Carlos Mendes",
    status: "Confirmado",
    color: "bg-emerald-100 text-emerald-700",
    initials: "JF",
    avatar: "bg-pink-100 text-pink-700",
  },
  {
    id: 4,
    name: "Pedro Almeida",
    service: "Consulta de rotina",
    time: "14:30",
    professional: "Dra. Ana Costa",
    status: "Cancelado",
    color: "bg-red-100 text-red-700",
    initials: "PA",
    avatar: "bg-orange-100 text-orange-700",
  },
];

export function AppointmentsList() {
  return (
    <section className="rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div className="flex items-center justify-between border-b border-slate-100 p-5 sm:px-6">
        <div>
          <h3 className="font-bold text-slate-800">
            Próximos agendamentos
          </h3>
          <p className="mt-1 text-xs text-slate-500">
            Lista ilustrativa de atendimentos
          </p>
        </div>

        <button
          type="button"
          aria-label="Mais opções"
          className="rounded-lg p-2 text-slate-400 hover:bg-slate-100"
        >
          <MoreHorizontal size={21} />
        </button>
      </div>

      <div className="divide-y divide-slate-100">
        {appointments.map((appointment) => (
          <div
            key={appointment.id}
            className="flex flex-col gap-3 p-5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:px-6"
          >
            <div className="flex min-w-0 items-center gap-3">
              <div
                className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-xs font-bold ${appointment.avatar}`}
              >
                {appointment.initials}
              </div>

              <div className="min-w-0">
                <p className="truncate text-sm font-semibold text-slate-800">
                  {appointment.name}
                </p>
                <p className="mt-1 truncate text-xs text-slate-500">
                  {appointment.service} · {appointment.professional}
                </p>
              </div>
            </div>

            <div className="flex items-center justify-between gap-3 sm:justify-end">
              <div className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                <Clock3 size={14} />
                {appointment.time}
              </div>

              <span
                className={`rounded-full px-3 py-1.5 text-xs font-semibold ${appointment.color}`}
              >
                {appointment.status}
              </span>
            </div>
          </div>
        ))}
      </div>

      <div className="border-t border-slate-100 px-5 py-4 text-center sm:px-6">
        <span className="text-xs text-slate-400">
          Os nomes e horários desta lista são fictícios.
        </span>
      </div>
    </section>
  );
}
