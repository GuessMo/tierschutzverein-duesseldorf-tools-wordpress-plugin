import { IconDots } from "@tabler/icons-react";
import {
  Button,
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@web-app-kit/frontend/ui";
import type { Conversation } from "@/types";
import type { ActionsMenuProps, MenuAction } from "./conversation.types";

function menuItems(conversation: Conversation): { action: MenuAction; label: string }[] {
  if (conversation.isTrashed) {
    return [
      { action: "restore", label: "Wiederherstellen" },
      { action: "delete", label: "Endgültig löschen" },
    ];
  }
  const isSpam = conversation.calcStatus === "spam";
  const isBlocked = conversation.calcStatus === "blocked";
  return [
    isSpam ? { action: "unspam", label: "Kein Spam" } : { action: "spam", label: "Als Spam markieren" },
    isBlocked
      ? { action: "unblock", label: "Blockierung aufheben" }
      : { action: "block", label: "Absender blockieren" },
    { action: "trash", label: "In den Papierkorb" },
  ];
}

export function ActionsMenu({ conversation, onAction }: ActionsMenuProps) {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" size="icon-sm" aria-label="Weitere Aktionen">
          <IconDots aria-hidden />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        {menuItems(conversation).map((item) => (
          <DropdownMenuItem
            key={item.action}
            variant={item.action === "delete" ? "destructive" : "default"}
            onSelect={() => onAction(item.action)}
          >
            {item.label}
          </DropdownMenuItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
