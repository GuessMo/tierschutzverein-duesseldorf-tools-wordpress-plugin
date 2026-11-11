import { IconExternalLink, IconPaw } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { AnimalCardProps } from "./conversation.types";

export function AnimalCard({ animal }: AnimalCardProps) {
  return (
    <div className="flex items-center gap-3 rounded-lg border p-2.5">
      {animal.thumbUrl ? (
        <img src={animal.thumbUrl} alt="" className="size-12 rounded-md object-cover" />
      ) : (
        <span className="flex size-12 items-center justify-center rounded-md bg-muted">
          <IconPaw aria-hidden className="size-6 text-muted-foreground" />
        </span>
      )}
      <div className="flex min-w-0 flex-1 flex-col">
        <span className="truncate font-medium">{animal.name}</span>
        <span className="truncate text-sm text-muted-foreground">{animal.facts.join(" · ")}</span>
      </div>
      {animal.viewUrl && (
        <Button asChild variant="outline" size="sm">
          <a href={animal.viewUrl} target="_blank" rel="noreferrer">
            <IconExternalLink data-icon="inline-start" />
            Tierseite
          </a>
        </Button>
      )}
    </div>
  );
}
