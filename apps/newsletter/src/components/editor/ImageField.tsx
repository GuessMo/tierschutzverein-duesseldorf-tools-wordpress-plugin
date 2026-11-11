import { useId, useState, type ChangeEvent } from "react";
import { IconPhotoUp, IconTrash } from "@tabler/icons-react";
import { Button, Input, Label, toast } from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import type { BlockData } from "@/types";

interface ImageFieldProps {
  data: BlockData;
  onChange: (data: BlockData) => void;
}

export function ImageField({ data, onChange }: ImageFieldProps) {
  const altId = useId();
  const fileId = useId();
  const [isUploading, setIsUploading] = useState(false);
  const upload = async (event: ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    event.target.value = "";
    if (!file) return;
    setIsUploading(true);
    try {
      const image = await newsletterApi.uploadImage(file);
      onChange({ ...data, id: image.id, url: image.url });
    } catch {
      toast.error("Das Bild konnte nicht hochgeladen werden.");
    } finally {
      setIsUploading(false);
    }
  };
  return (
    <div className="flex flex-col gap-3">
      {data.url && (
        <img
          src={data.url}
          alt=""
          className="max-h-64 self-start rounded-lg border object-contain"
        />
      )}
      <div className="flex flex-wrap gap-2">
        <Button asChild variant="outline" size="sm" disabled={isUploading}>
          <label htmlFor={fileId}>
            <IconPhotoUp data-icon="inline-start" />
            {isUploading ? "Wird hochgeladen …" : data.id ? "Anderes Bild" : "Bild hochladen"}
          </label>
        </Button>
        <input
          id={fileId}
          type="file"
          accept="image/*"
          className="sr-only"
          onChange={(e) => void upload(e)}
        />
        {data.id ? (
          <Button
            type="button"
            variant="ghost"
            size="sm"
            onClick={() => onChange({ alt: data.alt })}
          >
            <IconTrash data-icon="inline-start" />
            Bild entfernen
          </Button>
        ) : null}
      </div>
      <div className="flex flex-col gap-1.5">
        <Label htmlFor={altId}>Bildbeschreibung (Alt-Text)</Label>
        <Input
          id={altId}
          value={data.alt ?? ""}
          onChange={(e) => onChange({ ...data, alt: e.target.value })}
        />
      </div>
    </div>
  );
}
