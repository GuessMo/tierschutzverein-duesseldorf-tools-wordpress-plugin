import type { PanelSectionProps } from "./controls.types";

export function PanelSection({ title, children }: PanelSectionProps) {
  return (
    <section className="flex flex-col gap-3 border-b p-4">
      <h2 className="text-sm font-semibold">{title}</h2>
      {children}
    </section>
  );
}
