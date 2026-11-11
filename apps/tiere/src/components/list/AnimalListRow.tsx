import { cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { AnimalThumb, StateBadge } from "@/components/common";
import { POST_STATUS_LABELS } from "@/config/labels";
import { animalHash } from "@/hooks";
import { stateOf } from "@/lib";
import { animalName, speciesLine } from "./animalText";
import type { AnimalItemProps } from "./list.types";

const ROW_CLASSES = [
  "grid w-full grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 rounded-md px-3 py-2",
  "md:grid-cols-[auto_minmax(0,2fr)_minmax(0,2fr)_auto_7rem]",
].join(" ");

export function AnimalListRow({ animal, options }: AnimalItemProps) {
  return (
    <a href={animalHash(animal.id)} className={cn(INTERACTIVE_SURFACE_CLASSES, ROW_CLASSES)}>
      <AnimalThumb src={animal.thumb} />
      <span className="flex min-w-0 flex-col">
        <span className="truncate font-medium">{animalName(animal)}</span>
        {animal.internal_id && (
          <span className="truncate text-xs text-muted-foreground tabular-nums">
            {animal.internal_id}
          </span>
        )}
      </span>
      <span className="hidden truncate text-sm text-muted-foreground md:block">
        {speciesLine(animal)}
      </span>
      <StateBadge status={stateOf(animal, options)} />
      <span className="hidden text-sm md:block">{POST_STATUS_LABELS[animal.status]}</span>
    </a>
  );
}
