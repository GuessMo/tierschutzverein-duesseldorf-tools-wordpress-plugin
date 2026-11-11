import { FormError } from "@/components/common";
import { ImageTile } from "./ImageTile";
import type { ImageGalleryProps } from "./media.types";
import { NoUploadHint } from "./NoUploadHint";
import { UploadButton } from "./UploadButton";
import { useImageUpload } from "./useImageUpload";

export function ImageGallery(props: ImageGalleryProps) {
  const { id, label, images, animalId, canUpload, onChange } = props;
  const { isUploading, error, upload } = useImageUpload(animalId);
  const add = async (files: File[]) => {
    const uploaded = await upload(files);
    if (uploaded.length > 0) onChange([...images, ...uploaded]);
  };
  return (
    <fieldset className="flex flex-col gap-3" aria-busy={isUploading}>
      <legend className="mb-3 text-sm font-medium">{label}</legend>
      {images.length > 0 ? (
        <ul className="grid grid-cols-[repeat(auto-fill,minmax(8rem,1fr))] gap-3">
          {images.map((image, index) => (
            <ImageTile
              key={image.id}
              image={image}
              removeLabel={`Bild ${index + 1} entfernen`}
              onRemove={() => onChange(images.filter((item) => item.id !== image.id))}
            />
          ))}
        </ul>
      ) : (
        <p className="text-sm text-muted-foreground">Noch keine Bilder.</p>
      )}
      <div>
        <UploadButton
          id={`${id}-file`}
          label={isUploading ? "Lädt hoch …" : "Bilder hinzufügen"}
          multiple
          disabled={!canUpload || isUploading}
          onFiles={(files) => void add(files)}
        />
      </div>
      {!canUpload && <NoUploadHint />}
      <FormError message={error} />
    </fieldset>
  );
}
