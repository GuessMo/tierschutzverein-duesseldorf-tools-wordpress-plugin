import type { DragEvent } from "react";
import { cn } from "@web-app-kit/frontend/ui";
import type { TileDropZoneProps } from "./media.types";

function allowDrop(event: DragEvent) {
  event.preventDefault();
  event.dataTransfer.dropEffect = "move";
}

export function TileDropZone({ onDropHere, className, children }: TileDropZoneProps) {
  return (
    <li className={cn("rounded-lg transition-opacity", className)}>
      <div role="presentation" onDragOver={allowDrop} onDrop={onDropHere} className="flex flex-col gap-2">
        {children}
      </div>
    </li>
  );
}
