import { cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { symbolFor } from "@/config/symbols";
import type { LauncherTool } from "@/types";

const ITEM_CLASSES = "flex flex-col items-center gap-2 rounded-lg px-2 py-3 text-center";

export function LauncherItem({ tool }: { tool: LauncherTool }) {
  const Symbol = symbolFor(tool.symbol);

  return (
    <a
      href={tool.url}
      target="_blank"
      rel="noopener"
      className={cn(INTERACTIVE_SURFACE_CLASSES, ITEM_CLASSES)}
    >
      <Symbol aria-hidden className="size-7 text-primary" stroke={1.5} />
      <span className="line-clamp-2 w-full text-xs break-words">{tool.title}</span>
      <span className="sr-only">(öffnet in neuem Tab)</span>
    </a>
  );
}
