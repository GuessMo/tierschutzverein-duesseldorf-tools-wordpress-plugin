import { IconCircleCheck, IconLayoutOff } from "@tabler/icons-react";
import { useSpaceStore } from "@/store/spaceStore";

export function SpaceList() {
  const count = useSpaceStore((state) => state.overflowIds.length);
  if (count === 0) {
    return (
      <p className="flex items-center gap-2 text-sm text-muted-foreground">
        <IconCircleCheck className="size-4" />
        Alle Inhalte haben Platz.
      </p>
    );
  }
  return (
    <p className="flex items-center gap-2 text-sm font-medium text-destructive">
      <IconLayoutOff className="size-4" />
      {count === 1 ? "1 Bereich läuft über" : `${count} Bereiche laufen über`} (rot markiert)
    </p>
  );
}
