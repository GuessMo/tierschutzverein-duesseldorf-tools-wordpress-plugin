import { Card, CardContent } from "@web-app-kit/frontend/ui";
import type { StatTileProps } from "./charts.types";

export function StatTile({ label, value }: StatTileProps) {
  return (
    <Card size="sm">
      <CardContent className="flex flex-col gap-1">
        <span className="text-sm text-muted-foreground">{label}</span>
        <span className="text-2xl font-semibold tabular-nums">{value}</span>
      </CardContent>
    </Card>
  );
}
