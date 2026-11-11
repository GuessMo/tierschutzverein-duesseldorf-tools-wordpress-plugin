import { cn } from "./cn.js";
import type { MetaRowProps } from "./meta-row.types.js";

const META_ROW_CLASSES = "flex flex-wrap items-center gap-2 text-sm";

export function MetaRow({ label, className, children }: MetaRowProps) {
  const groupProps = label ? { role: "group", "aria-label": label } : {};

  return (
    <div data-slot="meta-row" className={cn(META_ROW_CLASSES, className)} {...groupProps}>
      {label && <span className="font-semibold">{label}</span>}
      {children}
    </div>
  );
}
