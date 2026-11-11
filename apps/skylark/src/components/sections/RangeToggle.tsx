import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import type { AnalyticsRange } from "@/types";
import type { RangeToggleProps } from "./sections.types";

const RANGES: AnalyticsRange[] = [7, 30, 90];

export function RangeToggle({ value, onChange }: RangeToggleProps) {
  return (
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label="Zeitraum"
      value={String(value)}
      onValueChange={(next) => next && onChange(Number(next) as AnalyticsRange)}
    >
      {RANGES.map((range) => (
        <ToggleGroupItem key={range} value={String(range)}>
          {range} Tage
        </ToggleGroupItem>
      ))}
    </ToggleGroup>
  );
}
