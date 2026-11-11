import { useState } from "react";
import { moveItem } from "@/lib";
import type { AnimalImage } from "@/types";
import { ImageTile } from "./ImageTile";
import type { GalleryListProps } from "./media.types";

export function GalleryList({ images, onChange }: GalleryListProps) {
  const [dragIndex, setDragIndex] = useState<number | null>(null);
  const move = (from: number, to: number) => onChange(moveItem<AnimalImage>(images, from, to));
  const drop = (to: number) => {
    if (dragIndex !== null) move(dragIndex, to);
    setDragIndex(null);
  };
  return (
    <ul className="grid grid-cols-[repeat(auto-fill,minmax(8rem,1fr))] gap-3">
      {images.map((image, index) => (
        <ImageTile
          key={image.id}
          image={image}
          position={index}
          count={images.length}
          isDragging={dragIndex === index}
          onRemove={() => onChange(images.filter((item) => item.id !== image.id))}
          onMove={(to) => move(index, to)}
          onDragStart={() => setDragIndex(index)}
          onDragEnd={() => setDragIndex(null)}
          onDropHere={() => drop(index)}
        />
      ))}
    </ul>
  );
}
