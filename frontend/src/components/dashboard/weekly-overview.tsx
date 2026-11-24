
import { CalendarDays } from "lucide-react";

const weeklyData = [
  { day: "Seg", value: 45 },
  { day: "Ter", value: 70 },
  { day: "Qua", value: 55 },
  { day: "Qui", value: 90 },
  { day: "Sex", value: 65 },
  { day: "Sáb", value: 35 },
  { day: "Dom", value: 20 },
];

export function WeeklyOverview() {
  return (
    <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h3 className="font-bold text-slate-800">
            Visão semanal
          </h3>
          <p className="mt-1 text-xs text-slate-500">
            Distribuição demonstrativa de agendamentos
          </p>
        </div>

        <div className="flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700">
          <CalendarDays size={15} />
          Últimos 7 dias
        </div>
      </div>

      <div className="mt-8 flex h-56 items-end justify-between gap-2 sm:gap-4">
        {weeklyData.map((item) => (
          <div
            key={item.day}
            className="flex h-full flex-1 flex-col items-center justify-end gap-3"
          >
            <div className="flex h-full w-full items-end justify-center">
              <div
                className={`w-full max-w-12 rounded-t-lg transition-all duration-300 ${
                  item.day === "Qui"
                    ? "bg-blue-600"
                    : "bg-blue-200 hover:bg-blue-400"
                }`}
                style={{ height: `${item.value}%` }}
                title={`${item.value}%`}
              />
            </div>

            <span className="text-xs font-medium text-slate-500">
              {item.day}
            </span>
          </div>
        ))}
      </div>

      <div className="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
        <span className="h-2.5 w-2.5 rounded-full bg-blue-600" />
        <span className="text-xs text-slate-500">
          Volume ilustrativo de agendamentos
        </span>
      </div>
    </section>
  );
}
