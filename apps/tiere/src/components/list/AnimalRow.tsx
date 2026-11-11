import { Button, TableCell, TableRow } from "@web-app-kit/frontend/ui";
import { AnimalThumb, StateBadge } from "@/components/common";
import { POST_STATUS_LABELS } from "@/config/labels";
import { animalHash } from "@/hooks";
import { stateOf } from "@/lib";
import type { AnimalRowProps } from "./list.types";

const DATE_FORMAT = new Intl.DateTimeFormat("de-DE", { dateStyle: "medium" });

function speciesLine(species: string, breed: string): string {
  return [species, breed].filter(Boolean).join(" · ") || "–";
}

export function AnimalRow({ animal, options }: AnimalRowProps) {
  const name = animal.name || "Ohne Namen";
  return (
    <TableRow>
      <TableCell>
        <div className="flex items-center gap-3">
          <AnimalThumb src={animal.thumb} />
          <div className="flex min-w-0 flex-col">
            <Button asChild variant="link" className="h-auto justify-start px-0 text-foreground">
              <a href={animalHash(animal.id)}>{name}</a>
            </Button>
            {animal.internal_id && (
              <span className="text-xs text-muted-foreground tabular-nums">
                {animal.internal_id}
              </span>
            )}
          </div>
        </div>
      </TableCell>
      <TableCell className="hidden text-muted-foreground md:table-cell">
        {speciesLine(animal.species, animal.breed)}
      </TableCell>
      <TableCell>
        <StateBadge status={stateOf(animal, options)} />
      </TableCell>
      <TableCell className="hidden sm:table-cell">{POST_STATUS_LABELS[animal.status]}</TableCell>
      <TableCell className="hidden text-right text-muted-foreground tabular-nums lg:table-cell">
        {DATE_FORMAT.format(new Date(animal.modified))}
      </TableCell>
    </TableRow>
  );
}
