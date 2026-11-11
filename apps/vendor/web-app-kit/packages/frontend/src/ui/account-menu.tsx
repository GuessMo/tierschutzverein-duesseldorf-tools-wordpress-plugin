import { IconLogout } from "@tabler/icons-react";
import { Button } from "./button.js";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./dropdown-menu.js";
import { DropdownMenuItem } from "./dropdown-menu-items.js";
import type { AccountMenuProps } from "./account-menu.types.js";

export function AccountMenu({ name, initials, label, logoutLabel, onLogout }: AccountMenuProps) {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button size="icon" className="rounded-full text-xs" aria-label={label}>
          <span aria-hidden>{initials}</span>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuLabel>{name}</DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuItem onSelect={onLogout}>
          <IconLogout aria-hidden />
          {logoutLabel}
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
