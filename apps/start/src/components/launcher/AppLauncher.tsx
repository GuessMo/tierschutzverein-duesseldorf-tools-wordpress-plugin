import { IconGridDots } from "@tabler/icons-react";
import { Button, Popover, PopoverContent, PopoverTrigger } from "@web-app-kit/frontend/ui";
import type { LauncherTool } from "@/types";
import { LauncherItem } from "./LauncherItem";

export function AppLauncher({ tools }: { tools: LauncherTool[] }) {
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon" aria-label="Tools">
          <IconGridDots />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="end" className="w-96">
        <ul className="grid grid-cols-3 gap-1" aria-label="Tools">
          {tools.map((tool) => (
            <li key={tool.slug}>
              <LauncherItem tool={tool} />
            </li>
          ))}
        </ul>
      </PopoverContent>
    </Popover>
  );
}
