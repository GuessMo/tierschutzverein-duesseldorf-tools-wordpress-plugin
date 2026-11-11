import type { ReactNode } from "react";

interface StatsSectionProps {
  title: string;
  hint: string;
  kpis: ReactNode;
  children: ReactNode;
}

export function StatsSection({ title, hint, kpis, children }: StatsSectionProps) {
  return (
    <section className="flex flex-col gap-4">
      <div className="flex flex-col gap-1">
        <h2 className="text-lg font-semibold">{title}</h2>
        <p className="max-w-prose text-sm text-muted-foreground">{hint}</p>
      </div>
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-5">{kpis}</div>
      <div className="grid grid-flow-row-dense gap-4 md:grid-cols-2 xl:grid-cols-6 [&>*]:min-w-0">
        {children}
      </div>
    </section>
  );
}
