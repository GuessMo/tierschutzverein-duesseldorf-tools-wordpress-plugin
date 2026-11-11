import { IconLayoutGrid, IconList } from "@tabler/icons-react";
import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import type { ListView } from "@/types";
import type { ViewToggleProps } from "./list.types";

export function ViewToggle({ view, onChange }: ViewToggleProps) {
  return (
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label="Ansicht"
      value={view}
      onValueChange={(next) => next && onChange(next as ListView)}
    >
      <ToggleGroupItem value="list" aria-label="Liste">
        <IconList aria-hidden />
      </ToggleGroupItem>
      <ToggleGroupItem value="tiles" aria-label="Kacheln">
        <IconLayoutGrid aria-hidden />
      </ToggleGroupItem>
    </ToggleGroup>
  );
}
