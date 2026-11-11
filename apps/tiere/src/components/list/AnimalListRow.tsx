import { IconExternalLink } from "@tabler/icons-react";
import { Button, cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { AnimalThumb, StateBadge } from "@/components/common";
import { POST_STATUS_LABELS } from "@/config/labels";
import { animalHash } from "@/hooks";
import { stateOf } from "@/lib";
import { animalName, speciesLine } from "./animalText";
import type { AnimalItemProps } from "./list.types";

const ROW_CLASSES = [
  "relative grid w-full grid-cols-[auto_minmax(0,1fr)_auto_2rem] items-center gap-3",
  "rounded-md px-3 py-2 md:grid-cols-[auto_minmax(0,2fr)_minmax(0,2fr)_auto_7rem_2rem]",
].join(" ");

const STRETCHED_LINK = [
  "truncate font-medium outline-none after:absolute after:inset-0 after:rounded-md",
  "hover:underline active:text-primary focus-visible:after:ring-2 focus-visible:after:ring-ring",
].join(" ");

export function AnimalListRow({ animal, options }: AnimalItemProps) {
  const name = animalName(animal);
  return (
    <div className={cn(INTERACTIVE_SURFACE_CLASSES, ROW_CLASSES)}>
      <AnimalThumb src={animal.thumb} />
      <span className="flex min-w-0 flex-col">
        <a href={animalHash(animal.id)} className={STRETCHED_LINK}>
          {name}
        </a>
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
      {animal.permalink ? (
        <Button asChild variant="ghost" size="icon-sm" className="relative z-10">
          <a
            href={animal.permalink}
            target="_blank"
            rel="noreferrer"
            aria-label={`${name}: Tierseite ansehen`}
            title="Tierseite ansehen"
          >
            <IconExternalLink aria-hidden />
          </a>
        </Button>
      ) : (
        <span aria-hidden />
      )}
    </div>
  );
}
