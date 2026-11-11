import { IconPaw } from "@tabler/icons-react";
import { Card } from "@web-app-kit/frontend/ui";
import { StateBadge } from "@/components/common";
import { animalHash } from "@/hooks";
import { stateOf } from "@/lib";
import { animalName, speciesLine } from "./animalText";
import type { AnimalItemProps } from "./list.types";

function TileImage({ src }: { src: string }) {
  if (src) return <img src={src} alt="" className="aspect-square w-full object-cover" />;
  return (
    <span className="flex aspect-square w-full items-center justify-center bg-muted text-muted-foreground">
      <IconPaw aria-hidden className="size-10" stroke={1.5} />
    </span>
  );
}

export function AnimalTile({ animal, options }: AnimalItemProps) {
  return (
    <Card interactive asChild className="h-full gap-2 pt-0">
      <a href={animalHash(animal.id)}>
        <TileImage src={animal.thumb} />
        <span className="flex min-w-0 flex-col gap-1.5 px-3">
          <span className="truncate font-medium">{animalName(animal)}</span>
          <span className="truncate text-xs text-muted-foreground">{speciesLine(animal)}</span>
          <span>
            <StateBadge status={stateOf(animal, options)} />
          </span>
        </span>
      </a>
    </Card>
  );
}
