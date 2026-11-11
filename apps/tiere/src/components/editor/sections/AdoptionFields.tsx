import { CheckboxField, SelectField } from "@/components/common";
import { MEDIATORS } from "@/config/labels";
import { isDog } from "@/lib";
import type { SectionProps } from "../editor.types";
import { RequirementsFields } from "./RequirementsFields";

export function AdoptionFields(props: SectionProps) {
  const { draft, options, onChange } = props;
  const status = options.adoption_statuses.find((item) => item.value === draft.adoption_status);
  return (
    <>
      <div className="grid gap-4 sm:grid-cols-2">
        <SelectField
          id="animal-mediator"
          label="Vermittelt durch"
          value={draft.mediator}
          options={MEDIATORS}
          onValueChange={(mediator) => onChange({ mediator })}
        />
        <SelectField
          id="animal-adoption-status"
          label="Vermittlungsstatus"
          value={draft.adoption_status}
          options={options.adoption_statuses}
          hint={status?.text}
          onValueChange={(adoption_status) => onChange({ adoption_status })}
        />
      </div>
      {isDog(options, draft.breeds) && (
        <CheckboxField
          id="animal-nori"
          label="Nimmt am Nori-Projekt teil"
          checked={draft.participates_in_nori}
          onCheckedChange={(participates_in_nori) => onChange({ participates_in_nori })}
        />
      )}
      <RequirementsFields {...props} />
    </>
  );
}
