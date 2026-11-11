import { IconExternalLink } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { Entry } from "@/types";

export function EntryTitle({ entry }: { entry: Entry }) {
  if (!entry.url) return <h3 className="text-sm font-semibold">{entry.title}</h3>;
  return (
    <h3 className="text-sm font-semibold">
      <Button
        asChild
        variant="link"
        className="h-auto px-0 text-left whitespace-normal text-foreground"
      >
        <a href={entry.url} target="_blank" rel="noopener noreferrer">
          {entry.title}
          <IconExternalLink aria-hidden data-icon="inline-end" />
          <span className="sr-only">(öffnet in neuem Tab)</span>
        </a>
      </Button>
    </h3>
  );
}
