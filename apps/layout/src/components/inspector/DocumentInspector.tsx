import { ToneSwatches } from "@/components/controls";
import { useEditorStore } from "@/store/editorStore";
import { ContrastList } from "./ContrastList";
import { SpaceList } from "./SpaceList";

export function DocumentInspector() {
  const tone = useEditorStore((state) => state.document?.tone ?? "white");
  const apply = useEditorStore((state) => state.apply);

  return (
    <div className="flex flex-col gap-5">
      <ToneSwatches
        label="Seitenhintergrund"
        value={tone}
        allowTransparent={false}
        onChange={(value) => apply((document) => ({ ...document, tone: value }))}
      />
      <div className="flex flex-col gap-2 border-t pt-4">
        <span className="text-xs font-medium text-muted-foreground">Barrierefreiheit</span>
        <ContrastList />
        <SpaceList />
      </div>
      <p className="text-xs text-muted-foreground">
        Klicke auf der Fläche einen Bereich, eine Spalte oder ein Element an, um es zu bearbeiten.
      </p>
    </div>
  );
}
