import type { ReactNode } from "react";
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

export interface TileDropZoneProps {
  onDropHere: () => void;
  className?: string;
  children: ReactNode;
}

export interface GalleryListProps {
  images: AnimalImage[];
  onChange: (images: AnimalImage[]) => void;
}

export interface ImageTileProps {
  image: AnimalImage;
  position: number;
  count: number;
  isDragging: boolean;
  onRemove: () => void;
  onMove: (to: number) => void;
  onDragStart: () => void;
  onDragEnd: () => void;
  onDropHere: () => void;
}

export interface ImageUpload {
  isUploading: boolean;
  error: string | null;
  upload: (files: File[]) => Promise<AnimalImage[]>;
}
