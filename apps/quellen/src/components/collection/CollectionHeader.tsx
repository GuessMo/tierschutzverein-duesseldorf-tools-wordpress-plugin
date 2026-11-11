import { Button } from "@web-app-kit/frontend/ui";

export function CollectionHeader({ name }: { name: string }) {
  return (
    <nav aria-label="Brotkrumen" className="min-w-0">
      <ol className="flex min-w-0 items-center gap-1 text-sm">
        <li>
          <Button asChild variant="ghost" size="sm">
            <a href="#">Sammlungen</a>
          </Button>
        </li>
        <li aria-hidden className="text-muted-foreground">
          /
        </li>
        <li aria-current="page" className="truncate px-1 font-medium">
          {name}
        </li>
      </ol>
    </nav>
  );
}
