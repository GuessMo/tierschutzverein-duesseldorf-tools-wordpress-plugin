import { IconTrash } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { ImageTileProps } from "./media.types";

export function ImageTile({ image, removeLabel, onRemove }: ImageTileProps) {
  return (
    <li className="flex flex-col gap-2">
      <img
        src={image.thumb || image.large}
        alt={image.alt}
        className="aspect-square w-full rounded-md border object-cover"
      />
      <Button type="button" variant="ghost" size="sm" aria-label={removeLabel} onClick={onRemove}>
        <IconTrash data-icon="inline-start" />
        Entfernen
      </Button>
    </li>
  );
}
