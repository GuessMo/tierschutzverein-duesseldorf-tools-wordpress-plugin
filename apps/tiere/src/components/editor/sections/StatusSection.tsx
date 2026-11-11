import { FormSection, SelectField } from "@/components/common";
import { RECORD_TYPES } from "@/config/labels";
import type { RecordType } from "@/types";
import type { SectionProps } from "../editor.types";
import { AdoptionFields } from "./AdoptionFields";
import { ContactFields } from "./ContactFields";
import { MissingFields } from "./MissingFields";

export function StatusSection(props: SectionProps) {
  const { draft, onChange } = props;
  const isMissing = draft.record_type === "vermisst";
  const showContact = isMissing || draft.mediator === "privat";
  return (
    <FormSection
      id="status"
      title="Status und Vermittlung"
      description="Lege fest, ob das Tier vermittelt wird oder als vermisst gemeldet ist."
    >
      <div className="grid gap-4 sm:grid-cols-2">
        <SelectField
          id="animal-record-type"
          label="Art des Eintrags"
          value={draft.record_type}
          options={RECORD_TYPES}
          onValueChange={(value) => onChange({ record_type: value as RecordType })}
        />
      </div>
      {isMissing ? <MissingFields {...props} /> : <AdoptionFields {...props} />}
      {showContact && <ContactFields {...props} />}
    </FormSection>
  );
}
