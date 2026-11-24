
import type { LucideIcon } from "lucide-react";
import { ArrowUpRight, ArrowDownRight } from "lucide-react";

interface StatCardProps {
  title: string;
  value: string;
  description: string;
  icon: LucideIcon;
  color: "blue" | "green" | "orange" | "purple";
  trend?: string;
  trendDirection?: "up" | "down";
}

const colorStyles = {
  blue: "bg-blue-50 text-blue-600",
  green: "bg-emerald-50 text-emerald-600",
  orange: "bg-orange-50 text-orange-600",
  purple: "bg-purple-50 text-purple-600",
};

export function StatCard({
  title,
  value,
  description,
  icon: Icon,
  color,
  trend,
  trendDirection = "up",
}: StatCardProps) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
      <div className="flex items-start justify-between">
        <div>
          <p className="text-sm font-medium text-slate-500">{title}</p>
          <h3 className="mt-3 text-3xl font-bold tracking-tight text-slate-800">
            {value}
          </h3>
        </div>

        <div className={`rounded-xl p-3 ${colorStyles[color]}`}>
          <Icon size={23} />
        </div>
      </div>

      <div className="mt-4 flex flex-wrap items-center gap-2 text-xs">
        {trend && (
          <span
            className={`inline-flex items-center gap-1 font-semibold ${
              trendDirection === "up" ? "text-emerald-600" : "text-red-500"
            }`}
          >
            {trendDirection === "up" ? (
              <ArrowUpRight size={14} />
            ) : (
              <ArrowDownRight size={14} />
            )}
            {trend}
          </span>
        )}

        <span className="text-slate-400">{description}</span>
      </div>
    </div>
  );
}
