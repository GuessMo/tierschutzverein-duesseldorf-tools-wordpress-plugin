import { IconSettings } from "@tabler/icons-react";
import {
  Button,
  Label,
  Popover,
  PopoverContent,
  PopoverTrigger,
  Switch,
} from "@web-app-kit/frontend/ui";
import type { ChartPrefs } from "./chartTypes";
import { isColored, supportsColor, supportsGrid } from "./chartTypes";

interface ChartSettingsProps {
  id: string;
  prefs: ChartPrefs;
  onGrid: (grid: boolean) => void;
  onColored: (colored: boolean) => void;
}

export function ChartSettings({ id, prefs, onGrid, onColored }: ChartSettingsProps) {
  const hasGrid = supportsGrid(prefs.type);
  const hasColor = supportsColor(prefs.type);
  if (!hasGrid && !hasColor) return null;
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon-sm" aria-label="Diagramm-Einstellungen">
          <IconSettings aria-hidden />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="end" className="flex w-56 flex-col gap-3">
        {hasGrid && (
          <div className="flex items-center justify-between gap-3">
            <Label htmlFor={`${id}-grid`}>Gitterlinien</Label>
            <Switch id={`${id}-grid`} checked={prefs.grid} onCheckedChange={onGrid} />
          </div>
        )}
        {hasColor && (
          <div className="flex items-center justify-between gap-3">
            <Label htmlFor={`${id}-color`}>Farbig</Label>
            <Switch id={`${id}-color`} checked={isColored(prefs)} onCheckedChange={onColored} />
          </div>
        )}
      </PopoverContent>
    </Popover>
  );
}
