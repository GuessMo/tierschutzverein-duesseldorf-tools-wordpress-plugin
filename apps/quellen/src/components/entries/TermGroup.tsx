import { Checkbox } from "@web-app-kit/frontend/ui";
import { flattenTerms } from "@/lib/terms";
import type { TermGroupProps } from "./entries.types";

const INDENT_REM = 1.25;

export function TermGroup({ taxonomy, terms, selected, onToggle }: TermGroupProps) {
  const nodes = flattenTerms(terms);
  return (
    <fieldset className="flex flex-col gap-2">
      <legend className="mb-1 text-sm font-medium">{taxonomy.name}</legend>
      {nodes.length === 0 && (
        <p className="text-xs text-muted-foreground">Diese Taxonomie hat noch keine Begriffe.</p>
      )}
      {nodes.map(({ term, depth }) => (
        <label
          key={term.id}
          className="flex items-center gap-2 text-sm"
          style={{ paddingLeft: `${depth * INDENT_REM}rem` }}
        >
          <Checkbox
            checked={selected.includes(term.id)}
            onCheckedChange={(state) => onToggle(term.id, state === true)}
          />
          {term.name}
        </label>
      ))}
    </fieldset>
  );
}
