import { IconPhoto } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import { FormError } from "@/components/common";
import type { ImageSlotProps } from "./media.types";
import { NoUploadHint } from "./NoUploadHint";
import { UploadButton } from "./UploadButton";
import { useImageUpload } from "./useImageUpload";

export function ImageSlot(props: ImageSlotProps) {
  const { id, label, image, animalId, canUpload, onChange } = props;
  const { isUploading, error, upload } = useImageUpload(animalId);
  const replace = async (files: File[]) => {
    const [uploaded] = await upload(files.slice(0, 1));
    if (uploaded) onChange(uploaded);
  };
  return (
    <fieldset className="flex flex-col gap-3" aria-busy={isUploading}>
      <legend className="mb-3 text-sm font-medium">{label}</legend>
      <div className="flex flex-wrap items-end gap-4">
        {image ? (
          <img src={image.large || image.thumb} alt={image.alt} className="h-40 w-auto max-w-full rounded-md border object-cover" />
        ) : (
          <span className="flex h-40 w-56 items-center justify-center rounded-md border border-dashed text-muted-foreground">
            <IconPhoto aria-hidden className="size-8" />
          </span>
        )}
        <div className="flex flex-wrap gap-2">
          <UploadButton
            id={`${id}-file`}
            label={isUploading ? "Lädt hoch …" : image ? "Bild ersetzen" : "Bild hochladen"}
            disabled={!canUpload || isUploading}
            onFiles={(files) => void replace(files)}
          />
          {image && (
            <Button type="button" variant="ghost" size="sm" onClick={() => onChange(null)}>
              Entfernen
            </Button>
          )}
        </div>
      </div>
      {!canUpload && <NoUploadHint />}
      <FormError message={error} />
    </fieldset>
  );
}
