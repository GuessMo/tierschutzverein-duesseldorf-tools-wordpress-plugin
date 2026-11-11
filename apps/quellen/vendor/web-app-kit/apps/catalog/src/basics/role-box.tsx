import { cn } from "@web-app-kit/frontend/ui";
import type { ColorRole } from "./basics.types";
import { contrastLabel } from "./contrast-label";
import { useRenderedColor } from "./use-rendered-color";

const BOX_CLASSES = "flex min-h-24 flex-col justify-between gap-2 rounded-md p-3 text-sm";

export function RoleBox({ name, className, kind, minimum }: ColorRole) {
  const { ref, color } = useRenderedColor<HTMLDivElement>(kind);
  const borderClass = kind === "line" ? "border-2" : "border border-border";
  return (
    <div ref={ref} className={cn(BOX_CLASSES, borderClass, className)}>
      <span className="font-medium">{name}</span>
      <span className="flex flex-col gap-0.5 text-xs">
        <span className="truncate font-mono" title={color?.value}>
          {color?.value}
        </span>
        <span>{contrastLabel(color?.ratio ?? null, minimum)}</span>
      </span>
    </div>
  );
}
