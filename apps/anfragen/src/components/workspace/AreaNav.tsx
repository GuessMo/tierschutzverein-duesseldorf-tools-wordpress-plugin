import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import type { Area } from "@/hooks";

interface AreaNavProps {
  area: Area;
  onChange: (area: Area) => void;
}

export function AreaNav({ area, onChange }: AreaNavProps) {
  return (
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label="Bereich"
      value={area}
      onValueChange={(next) => next && onChange(next as Area)}
    >
      <ToggleGroupItem value="conversations">Gespräche</ToggleGroupItem>
      <ToggleGroupItem value="relayed" aria-label="Weitergeleitet an Halter">
        <span className="sm:hidden">An Halter</span>
        <span className="hidden sm:inline">Weitergeleitet an Halter</span>
      </ToggleGroupItem>
    </ToggleGroup>
  );
}
