import { IconArrowLeft, IconArrowRight, IconGripVertical, IconTrash } from "@tabler/icons-react";
import { Button, cn } from "@web-app-kit/frontend/ui";
import type { ImageTileProps } from "./media.types";
import { TileDropZone } from "./TileDropZone";

export function ImageTile(props: ImageTileProps) {
  const { image, position, count, isDragging, onRemove, onMove } = props;
  const label = `Bild ${position + 1} von ${count}`;
  return (
    <TileDropZone onDropHere={props.onDropHere} className={cn(isDragging && "opacity-40")}>
      <div className="relative">
        <img
          src={image.thumb || image.large}
          alt={image.alt}
          className="aspect-square w-full rounded-md border object-cover"
        />
        <Button
          type="button"
          variant="secondary"
          size="sm"
          draggable
          onDragStart={props.onDragStart}
          onDragEnd={props.onDragEnd}
          aria-label={`${label} verschieben (ziehen)`}
          className="absolute top-1.5 left-1.5 h-6 cursor-grab gap-0.5 px-1 tabular-nums active:cursor-grabbing"
        >
          <IconGripVertical aria-hidden />
          {position + 1}
        </Button>
      </div>
      <div className="flex items-center justify-between gap-1">
        <Button
          type="button"
          variant="ghost"
          size="icon-sm"
          aria-label={`${label} nach vorn`}
          disabled={position === 0}
          onClick={() => onMove(position - 1)}
        >
          <IconArrowLeft aria-hidden />
        </Button>
        <Button type="button" variant="ghost" size="icon-sm" aria-label={`${label} entfernen`} onClick={onRemove}>
          <IconTrash aria-hidden />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon-sm"
          aria-label={`${label} nach hinten`}
          disabled={position === count - 1}
          onClick={() => onMove(position + 1)}
        >
          <IconArrowRight aria-hidden />
        </Button>
      </div>
    </TileDropZone>
  );
}
