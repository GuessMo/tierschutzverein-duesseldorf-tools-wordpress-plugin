import { IconPaw, IconX } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";

interface AnimalFilterChipProps {
  name: string;
  onClear: () => void;
}

export function AnimalFilterChip({ name, onClear }: AnimalFilterChipProps) {
  return (
    <div className="flex items-center gap-2 rounded-lg border bg-primary-subtle px-2.5 py-1.5 text-sm text-primary-subtle-foreground">
      <IconPaw aria-hidden className="size-4 shrink-0" />
      <span className="min-w-0 flex-1 truncate">Nur Anfragen zu {name || "diesem Tier"}</span>
      <Button variant="ghost" size="icon-xs" aria-label="Tier-Filter entfernen" onClick={onClear}>
        <IconX aria-hidden />
      </Button>
    </div>
  );
}
