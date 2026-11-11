import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import { LIST_HASH, STATS_HASH } from "@/hooks";

export function AreaNav({ isStats }: { isStats: boolean }) {
  return (
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label="Bereich"
      value={isStats ? "stats" : "animals"}
      onValueChange={(next) => {
        if (next) window.location.hash = next === "stats" ? STATS_HASH : LIST_HASH;
      }}
    >
      <ToggleGroupItem value="animals">Tiere</ToggleGroupItem>
      <ToggleGroupItem value="stats">Statistik</ToggleGroupItem>
    </ToggleGroup>
  );
}
