import { FormSection } from "@/components/common";
import type { SectionProps } from "../editor.types";
import { AdoptionFields } from "./AdoptionFields";
import { ContactFields } from "./ContactFields";
import { MissingFields } from "./MissingFields";

export function StatusSection(props: SectionProps) {
  const { draft } = props;
  const isMissing = draft.record_type === "vermisst";
  const showContact = isMissing || draft.mediator === "privat";
  return (
    <FormSection
      id="status"
      title={isMissing ? "Stand der Meldung" : "Status und Vermittlung"}
      description={
        isMissing
          ? "Halte fest, ob das Tier noch vermisst wird oder wieder zu Hause ist."
          : "Lege fest, wer vermittelt und wie weit die Vermittlung ist."
      }
    >
      {isMissing ? <MissingFields {...props} /> : <AdoptionFields {...props} />}
      {showContact && <ContactFields {...props} />}
    </FormSection>
  );
}
