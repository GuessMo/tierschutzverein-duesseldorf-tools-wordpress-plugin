import { CheckboxField, FormSection, TextAreaField, TextField } from "@/components/common";
import { ImageSlot } from "@/components/media";
import type { AnimalTdw } from "@/types";
import type { MediaSectionProps } from "../editor.types";

export function TdwSection({ draft, options, animalId, onChange }: MediaSectionProps) {
  const { tdw } = draft;
  const set = (next: Partial<AnimalTdw>) => onChange({ tdw: { ...tdw, ...next } });
  return (
    <FormSection id="tdw" title="Tier der Woche">
      <CheckboxField
        id="animal-tdw-enabled"
        label="Als Tier der Woche zeigen"
        checked={tdw.enabled}
        onCheckedChange={(enabled) => set({ enabled })}
      />
      {tdw.enabled && (
        <>
          <div className="grid gap-4 sm:grid-cols-2">
            <TextField
              id="animal-tdw-year"
              label="Jahr"
              type="number"
              min={2000}
              value={String(tdw.year)}
              onValueChange={(value) => set({ year: Number(value) })}
            />
            <TextField
              id="animal-tdw-week"
              label="Kalenderwoche"
              type="number"
              min={1}
              max={53}
              value={String(tdw.week)}
              onValueChange={(value) => set({ week: Number(value) })}
            />
          </div>
          <TextAreaField
            id="animal-tdw-text"
            label="Text"
            rows={6}
            value={tdw.text}
            onValueChange={(text) => set({ text })}
          />
          <ImageSlot
            id="animal-tdw-image"
            label="Bild für den Artikel"
            image={tdw.article_image}
            animalId={animalId}
            canUpload={options.caps.upload}
            onChange={(article_image) => set({ article_image })}
          />
        </>
      )}
    </FormSection>
  );
}
