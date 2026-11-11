import { Badge, cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import type { WaitLevel } from "@/types";
import type { ConversationItemProps } from "./list.types";

const WAIT_VARIANTS: Record<Exclude<WaitLevel, "">, "warning" | "destructive"> = {
  warn: "warning",
  alert: "destructive",
};

const ITEM_CLASSES = "flex w-full flex-col gap-1 rounded-md px-3 py-2.5";

export function ConversationItem({ item, isSelected, onSelect }: ConversationItemProps) {
  return (
    <button
      type="button"
      aria-current={isSelected ? "true" : undefined}
      onClick={onSelect}
      className={cn(INTERACTIVE_SURFACE_CLASSES, ITEM_CLASSES)}
    >
      <span className="flex items-center gap-2">
        {item.isOpen && (
          <span aria-label="offen" className="size-2 shrink-0 rounded-full bg-primary" />
        )}
        <span className={cn("min-w-0 flex-1 truncate", item.isOpen && "font-semibold")}>
          {item.name || "Ohne Namen"}
        </span>
        <span className="shrink-0 text-xs text-muted-foreground">{item.activityDay}</span>
      </span>
      <span className="flex items-center gap-2">
        <span className="min-w-0 flex-1 truncate text-sm text-muted-foreground">
          {item.animal || "Ohne Tier"}
        </span>
        {item.waitLevel && (
          <Badge variant={WAIT_VARIANTS[item.waitLevel]} title={item.waitTitle}>
            {item.waitLabel}
          </Badge>
        )}
      </span>
    </button>
  );
}
