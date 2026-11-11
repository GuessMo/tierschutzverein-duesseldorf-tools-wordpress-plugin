import { cn, INTERACTIVE_SURFACE_CLASSES, PageContainer } from "@web-app-kit/frontend/ui";
import { CATALOG_ENTRIES } from "../catalog/entries";
import { GROUP_LABELS, GROUP_ORDER } from "../catalog/groups";
import { componentPath } from "../routing/paths";
import { PageHeader } from "../shared/page-header";

const ITEM_CLASSES = cn(INTERACTIVE_SURFACE_CLASSES, "flex flex-col gap-0.5 rounded-md px-3 py-2");

function entriesOf(group: (typeof GROUP_ORDER)[number]) {
  return CATALOG_ENTRIES.filter((entry) => entry.group === group).sort((first, second) =>
    first.name.localeCompare(second.name, "de"),
  );
}

export function ComponentIndexPage() {
  return (
    <PageContainer size="lg" className="flex flex-col gap-8">
      <PageHeader title="Komponenten">
        Alle Bausteine aus @web-app-kit/frontend/ui, gruppiert nach Einsatz. Fehlt einer, kommt er
        in den Kit, nicht ins Tool.
      </PageHeader>
      <div className="grid gap-8 lg:grid-cols-2">
        {GROUP_ORDER.map((group) => (
          <section key={group} aria-labelledby={`gruppe-${group}`} className="flex flex-col gap-2">
            <h2 id={`gruppe-${group}`} className="text-sm font-medium text-muted-foreground">
              {GROUP_LABELS[group]}
            </h2>
            <ul className="flex flex-col gap-1">
              {entriesOf(group).map((entry) => (
                <li key={entry.slug}>
                  <a href={componentPath(entry.slug)} className={ITEM_CLASSES}>
                    <span className="font-medium">{entry.name}</span>
                    <span className="line-clamp-2 text-sm text-muted-foreground">
                      {entry.description}
                    </span>
                  </a>
                </li>
              ))}
            </ul>
          </section>
        ))}
      </div>
    </PageContainer>
  );
}
