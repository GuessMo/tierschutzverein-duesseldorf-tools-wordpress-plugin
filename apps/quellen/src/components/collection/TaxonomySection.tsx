import type { TaxonomySectionProps } from "./collection.types";
import { ActionMenu } from "./ActionMenu";
import { taxonomyActions } from "./menuActions";
import { TermList } from "./TermList";

export function TaxonomySection({ taxonomy, terms, onOpen }: TaxonomySectionProps) {
  const headingId = `taxonomy-${taxonomy.id}`;
  return (
    <section className="flex flex-col gap-1 py-2" aria-labelledby={headingId}>
      <div className="flex items-center gap-2 pl-2">
        <div className="min-w-0 flex-1">
          <h3 id={headingId} className="truncate text-sm font-semibold">
            {taxonomy.name}
          </h3>
          <p className="text-xs text-muted-foreground">
            {taxonomy.is_hierarchical ? "Mit Unterbegriffen" : "Schlagwörter"}
          </p>
        </div>
        <ActionMenu
          label={`Aktionen für ${taxonomy.name}`}
          actions={taxonomyActions(taxonomy, onOpen)}
        />
      </div>
      <TermList taxonomy={taxonomy} terms={terms} onOpen={onOpen} />
    </section>
  );
}
