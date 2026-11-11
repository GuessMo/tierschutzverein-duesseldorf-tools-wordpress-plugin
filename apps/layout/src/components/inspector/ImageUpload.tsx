import { Button } from "@web-app-kit/frontend/ui";
import { IconPhotoUp, IconX } from "@tabler/icons-react";
import { useId, useRef, useState, type ChangeEvent } from "react";
import { readImageFile } from "@/services/imageFile";
import type { ImageUploadProps } from "./inspector.types";

export function ImageUpload({ label, value, onChange }: ImageUploadProps) {
  const inputId = useId();
  const inputRef = useRef<HTMLInputElement>(null);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleFile = async (event: ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    event.target.value = "";
    if (!file) return;
    try {
      onChange(await readImageFile(file));
      setErrorMessage(null);
    } catch (error) {
      setErrorMessage((error as Error).message);
    }
  };

  return (
    <div className="flex flex-col gap-2">
      <span className="text-xs font-medium text-muted-foreground">{label}</span>
      {value && <img className="max-h-32 w-full rounded-md object-cover" src={value} alt="" />}
      <div className="flex gap-2">
        <Button variant="outline" size="sm" onClick={() => inputRef.current?.click()}>
          <IconPhotoUp data-icon="inline-start" />
          {value ? "Bild ersetzen" : "Bild wählen"}
        </Button>
        <input
          ref={inputRef}
          id={inputId}
          className="sr-only"
          type="file"
          accept="image/*"
          aria-label={label}
          onChange={handleFile}
        />
        {value && (
          <Button variant="ghost" size="sm" onClick={() => onChange(undefined)}>
            <IconX data-icon="inline-start" />
            Entfernen
          </Button>
        )}
      </div>
      {errorMessage && (
        <p className="text-xs text-destructive" role="alert">
          {errorMessage}
        </p>
      )}
    </div>
  );
}
