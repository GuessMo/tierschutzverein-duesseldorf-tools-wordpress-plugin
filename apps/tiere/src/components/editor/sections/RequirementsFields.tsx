import { CheckboxField } from "@/components/common";
import { requirementSet } from "@/lib";
import type { SectionProps } from "../editor.types";

function toggle(list: string[], value: string): string[] {
  return list.includes(value) ? list.filter((item) => item !== value) : [...list, value];
}

export function RequirementsFields({ draft, options, onChange }: SectionProps) {
  const set = requirementSet(options, draft.breeds);
  if (!set) return null;
  return (
    <fieldset className="flex flex-col">
      <legend className="mb-3 text-sm font-medium">{set.label}</legend>
      <div className="grid gap-3 sm:grid-cols-2">
        {set.options.map((option) => (
          <CheckboxField
            key={option.value}
            id={`animal-requirement-${option.value}`}
            label={option.label}
            checked={draft.adoption_requirements.includes(option.value)}
            onCheckedChange={() =>
              onChange({
                adoption_requirements: toggle(draft.adoption_requirements, option.value),
              })
            }
          />
        ))}
      </div>
    </fieldset>
  );
}
