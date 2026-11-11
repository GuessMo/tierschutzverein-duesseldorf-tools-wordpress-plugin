import { termsOf } from "@/lib/terms";
import type { TermPickerProps } from "./entries.types";
import { TermGroup } from "./TermGroup";

export function TermPicker({ taxonomies, terms, selected, onChange }: TermPickerProps) {
  const toggle = (id: number, isChecked: boolean) =>
    onChange(isChecked ? [...selected, id] : selected.filter((current) => current !== id));

  return (
    <>
      {taxonomies.map((taxonomy) => (
        <TermGroup
          key={taxonomy.id}
          taxonomy={taxonomy}
          terms={termsOf(terms, taxonomy.id)}
          selected={selected}
          onToggle={toggle}
        />
      ))}
    </>
  );
}
