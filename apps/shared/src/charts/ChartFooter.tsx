import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import { chartIcon } from "./chartIcons";
import { ChartSettings } from "./ChartSettings";
import type { ChartPrefs, ChartType } from "./chartTypes";
import { TYPE_LABELS } from "./chartTypes";

interface ChartFooterProps {
  id: string;
  types: ChartType[];
  prefs: ChartPrefs;
  onType: (type: ChartType) => void;
  onGrid: (grid: boolean) => void;
  onColored: (colored: boolean) => void;
}

export function ChartFooter({ id, types, prefs, onType, onGrid, onColored }: ChartFooterProps) {
  return (
    <div className="flex w-full items-center justify-between gap-2">
      <ToggleGroup
        type="single"
        variant="outline"
        size="sm"
        label="Darstellung"
        value={prefs.type}
        onValueChange={(next) => next && onType(next as ChartType)}
      >
        {types.map((type) => (
          <ToggleGroupItem
            key={type}
            value={type}
            aria-label={TYPE_LABELS[type]}
            title={TYPE_LABELS[type]}
          >
            {chartIcon(type)}
          </ToggleGroupItem>
        ))}
      </ToggleGroup>
      <ChartSettings id={id} prefs={prefs} onGrid={onGrid} onColored={onColored} />
    </div>
  );
}
