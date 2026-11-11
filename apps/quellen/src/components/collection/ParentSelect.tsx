import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import { Field } from "@/components/common";
import { descendantIds, flattenTerms } from "@/lib/terms";
import type { ParentSelectProps } from "./collection.types";

const ROOT = "root";

export function ParentSelect({ terms, termId, value, onChange }: ParentSelectProps) {
  const excluded = termId ? descendantIds(terms, termId) : new Set<number>();
  const options = flattenTerms(terms).filter(({ term }) => !excluded.has(term.id));

  return (
    <Field id="term-parent" label="Oberbegriff">
      <Select
        value={value === null ? ROOT : String(value)}
        onValueChange={(next) => onChange(next === ROOT ? null : Number(next))}
      >
        <SelectTrigger id="term-parent" className="w-full">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem value={ROOT}>Keiner (oberste Ebene)</SelectItem>
            {options.map(({ term, depth }) => (
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
