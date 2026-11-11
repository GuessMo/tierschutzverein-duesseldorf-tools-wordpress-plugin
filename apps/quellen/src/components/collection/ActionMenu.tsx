import { IconDots } from "@tabler/icons-react";
import {
  Button,
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@web-app-kit/frontend/ui";
import type { MenuAction } from "./collection.types";

export function ActionMenu({ label, actions }: { label: string; actions: MenuAction[] }) {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" size="icon-sm" aria-label={label}>
          <IconDots />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="min-w-48">
        {actions.map((action) => (
          <DropdownMenuItem
            key={action.label}
            variant={action.isDestructive ? "destructive" : "default"}
            onSelect={action.onSelect}
          >
            {action.label}
          </DropdownMenuItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
