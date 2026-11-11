import { IconRectangle, IconRectangleVertical } from "@tabler/icons-react";
import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import { resolveDimensions } from "@/config/dimensions";
import { useEditorStore } from "@/store/editorStore";
import type { OrientationId } from "@/types/document";

export function OrientationToggle() {
  const document = useEditorStore((state) => state.document);
  const apply = useEditorStore((state) => state.apply);
  if (!document || !resolveDimensions(document).isPrint) return null;

  return (
    <ToggleGroup
      type="single"
      variant="outline"
      spacing={0}
      label="Ausrichtung"
      value={document.orientation ?? "portrait"}
      onValueChange={(value) =>
        value && apply((current) => ({ ...current, orientation: value as OrientationId }))
      }
    >
      <ToggleGroupItem value="portrait" aria-label="Hochformat" title="Hochformat">
        <IconRectangleVertical />
      </ToggleGroupItem>
      <ToggleGroupItem value="landscape" aria-label="Querformat" title="Querformat">
        <IconRectangle />
      </ToggleGroupItem>
    </ToggleGroup>
  );
}
