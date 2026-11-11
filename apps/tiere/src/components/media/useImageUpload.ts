import { useState } from "react";
import { describeError } from "@web-app-kit/frontend";
import { animalsApi } from "@/api";
import type { AnimalImage } from "@/types";
import type { ImageUpload } from "./media.types";

const UPLOAD_ERROR = "Das Bild konnte nicht hochgeladen werden.";

export function useImageUpload(animalId: number | null): ImageUpload {
  const [isUploading, setIsUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const upload = async (files: File[]): Promise<AnimalImage[]> => {
    setIsUploading(true);
    setError(null);
    const uploaded: AnimalImage[] = [];
    try {
      for (const file of files) uploaded.push(await animalsApi.uploadImage(file, animalId));
    } catch (caught) {
      setError(describeError(caught, UPLOAD_ERROR));
    } finally {
      setIsUploading(false);
    }
    return uploaded;
  };

  return { isUploading, error, upload };
}
