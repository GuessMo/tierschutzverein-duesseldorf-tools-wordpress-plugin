import type { AnimalImage } from "@/types";

interface MediaFieldProps {
  id: string;
  label: string;
  animalId: number | null;
  canUpload: boolean;
}

export interface ImageSlotProps extends MediaFieldProps {
  image: AnimalImage | null;
  onChange: (image: AnimalImage | null) => void;
}

export interface ImageGalleryProps extends MediaFieldProps {
  images: AnimalImage[];
  onChange: (images: AnimalImage[]) => void;
}

export interface UploadButtonProps {
  id: string;
  label: string;
  multiple?: boolean;
  disabled: boolean;
  onFiles: (files: File[]) => void;
}

export interface ImageTileProps {
  image: AnimalImage;
  removeLabel: string;
  onRemove: () => void;
}

export interface ImageUpload {
  isUploading: boolean;
  error: string | null;
  upload: (files: File[]) => Promise<AnimalImage[]>;
}
