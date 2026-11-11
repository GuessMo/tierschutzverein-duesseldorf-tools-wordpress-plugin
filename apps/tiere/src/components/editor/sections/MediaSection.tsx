import { FormSection } from "@/components/common";
import { ImageGallery, ImageSlot } from "@/components/media";
import type { MediaSectionProps } from "../editor.types";

export function MediaSection({ draft, options, animalId, onChange }: MediaSectionProps) {
  const canUpload = options.caps.upload;
  return (
    <FormSection
      id="media"
      title="Bilder"
      description="Das Titelbild erscheint in Listen und oben auf der Tierseite."
    >
      <ImageSlot
        id="animal-featured-image"
        label="Titelbild"
        image={draft.featured_image}
        animalId={animalId}
        canUpload={canUpload}
        onChange={(featured_image) => onChange({ featured_image })}
      />
      <ImageGallery
        id="animal-images"
        label="Weitere Bilder"
        images={draft.images}
        animalId={animalId}
        canUpload={canUpload}
        onChange={(images) => onChange({ images })}
      />
    </FormSection>
  );
}
