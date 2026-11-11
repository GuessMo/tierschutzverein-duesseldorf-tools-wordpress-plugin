import { IconArrowBackUp, IconArrowForwardUp, IconChevronLeft } from "@tabler/icons-react";
import { Button, Input } from "@web-app-kit/frontend/ui";
import { AppControls } from "@/components/controls";
import { useEditorStore } from "@/store/editorStore";
import { useLibraryStore } from "@/store/libraryStore";
import { ContrastStatus } from "./ContrastStatus";
import type { TopBarProps } from "./editor.types";
import { ExportActions } from "./ExportActions";
import { FormatSelect } from "./FormatSelect";
import { OrientationToggle } from "./OrientationToggle";

export function EditorHeaderActions() {
  const name = useEditorStore((state) => state.document?.name ?? "");
  const canUndo = useEditorStore((state) => state.past.length > 0);
  const canRedo = useEditorStore((state) => state.future.length > 0);
  const { apply, undo, redo } = useEditorStore.getState();
  const open = useLibraryStore((state) => state.open);

  return (
    <>
      <Button variant="ghost" onClick={() => open(null)}>
        <IconChevronLeft data-icon="inline-start" />
        Dokumente
      </Button>
      <Input
        aria-label="Dokumentname"
        className="w-48 font-medium"
        value={name}
        onChange={(event) => apply((document) => ({ ...document, name: event.target.value }))}
      />
      <FormatSelect />
      <OrientationToggle />
      <Button
        variant="ghost"
        size="icon"
        aria-label="Rückgängig"
        onClick={undo}
        disabled={!canUndo}
      >
        <IconArrowBackUp />
      </Button>
      <Button
        variant="ghost"
        size="icon"
        aria-label="Wiederholen"
        onClick={redo}
        disabled={!canRedo}
      >
        <IconArrowForwardUp />
      </Button>
    </>
  );
}

export function EditorHeaderControls({ artboardRef }: TopBarProps) {
  return (
    <>
      <ContrastStatus />
      <ExportActions artboardRef={artboardRef} />
      <AppControls />
    </>
  );
}
