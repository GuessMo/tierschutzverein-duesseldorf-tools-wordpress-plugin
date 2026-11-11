import { Card } from "@web-app-kit/frontend/ui";
import { symbolFor } from "@/config/symbols";
import type { LauncherTool } from "@/types";

export function ToolTile({ tool }: { tool: LauncherTool }) {
  const Symbol = symbolFor(tool.symbol);

  return (
    <Card interactive asChild>
      <a
        href={tool.url}
        target="_blank"
        rel="noopener"
        className="flex flex-col items-center gap-3 px-4 py-6 text-center"
      >
        <Symbol aria-hidden className="size-8 text-primary" stroke={1.5} />
        <span className="font-medium">{tool.title}</span>
        <span className="sr-only">(öffnet in neuem Tab)</span>
      </a>
    </Card>
  );
}
