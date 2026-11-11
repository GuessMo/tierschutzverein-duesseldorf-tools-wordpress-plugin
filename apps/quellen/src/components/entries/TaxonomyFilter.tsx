import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import { Field } from "@/components/common";
import { flattenTerms } from "@/lib/terms";
import type { TaxonomyFilterProps } from "./entries.types";

const ALL = "all";

export function TaxonomyFilter({ taxonomy, terms, value, onChange }: TaxonomyFilterProps) {
  const id = `filter-taxonomy-${taxonomy.id}`;
  return (
    <Field id={id} label={taxonomy.name}>
      <Select
        value={value === undefined ? ALL : String(value)}
        onValueChange={(next) => onChange(next === ALL ? undefined : Number(next))}
      >
        <SelectTrigger id={id} className="w-full min-w-40">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem value={ALL}>Alle</SelectItem>
            {flattenTerms(terms).map(({ term, depth }) => (
              <SelectItem key={term.id} value={String(term.id)}>
                {`${"– ".repeat(depth)}${term.name}`}
              </SelectItem>
            ))}
          </SelectGroup>
        </SelectContent>
      </Select>
    </Field>
  );
}
