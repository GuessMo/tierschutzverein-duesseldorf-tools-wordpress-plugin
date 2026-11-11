import { CheckboxField, FormSection, SelectField, TextAreaField, TextField } from "@/components/common";
import { ImageGallery } from "@/components/media";
import type { MediaSectionProps } from "../editor.types";

export function StorySection({ draft, options, animalId, onChange }: MediaSectionProps) {
  return (
    <FormSection
      id="story"
      title="Nach der Vermittlung"
      description="Vermittlungsdaten und die Geschichte der neuen Besitzer."
    >
      <div className="grid gap-4 sm:grid-cols-2">
        <TextField
          id="animal-adoption-date"
          label="Vermittlungsdatum"
          type="date"
          value={draft.adoption_date}
          onValueChange={(adoption_date) => onChange({ adoption_date })}
        />
        <TextField
          id="animal-owner-feedback-date"
          label="Datum der Besitzer-Rückmeldung"
          type="date"
          value={draft.owner_feedback_date}
          onValueChange={(owner_feedback_date) => onChange({ owner_feedback_date })}
        />
        <SelectField
          id="animal-adoption-source"
          label="Aufmerksam geworden durch"
          value={draft.adoption_source}
          options={options.adoption_sources}
          onValueChange={(adoption_source) => onChange({ adoption_source })}
        />
        {draft.adoption_source === "other" && (
          <TextField
            id="animal-adoption-source-other"
            label="Andere Quelle"
            value={draft.adoption_source_other}
            onValueChange={(adoption_source_other) => onChange({ adoption_source_other })}
          />
        )}
      </div>
      <CheckboxField
        id="animal-show-in-frontend"
        label="Auf der Website als erfolgreich vermittelt zeigen"
        hint="Das Tier erscheint dann im Archiv der vermittelten Tiere. Dafür muss der Text ausgefüllt sein."
        checked={draft.show_in_frontend}
        onCheckedChange={(show_in_frontend) => onChange({ show_in_frontend })}
      />
      <TextAreaField
        id="animal-owner-text"
        label="Text der neuen Besitzer"
        rows={6}
        value={draft.owner_text}
        onValueChange={(owner_text) => onChange({ owner_text })}
      />
      <ImageGallery
        id="animal-owner-photos"
        label="Fotos der neuen Besitzer"
        images={draft.owner_photos}
        animalId={animalId}
        canUpload={options.caps.upload}
        onChange={(owner_photos) => onChange({ owner_photos })}
      />
    </FormSection>
  );
}
