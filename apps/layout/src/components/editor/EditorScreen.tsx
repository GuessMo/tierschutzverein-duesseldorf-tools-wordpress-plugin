import { AppShell } from "@web-app-kit/frontend/ui";
import { useRef } from "react";
import { Artboard } from "@/components/artboard";
import { Inspector } from "@/components/inspector";
import { resolveDimensions } from "@/config/dimensions";
import {
  useAutosave,
  useContrastCheck,
  useEditorShortcuts,
  useElementFit,
  useFitSize,
  useSpaceGuard,
} from "@/hooks";
import { selectActivePage, useEditorStore } from "@/store/editorStore";
import { EditorHeaderActions, EditorHeaderControls } from "./EditorHeader";
import { LeftPanel } from "./LeftPanel";

const CANVAS_CLASSES = [
  "editor-canvas grid h-full place-items-center overflow-auto bg-muted",
  "bg-[radial-gradient(var(--color-border)_1px,transparent_1px)] bg-size-[1.25rem_1.25rem]",
].join(" ");

export function EditorScreen() {
  const document = useEditorStore((state) => state.document);
  const select = useEditorStore((state) => state.select);
  const page = useEditorStore(selectActivePage);
  const canvasRef = useRef<HTMLDivElement>(null);
  const artboardRef = useRef<HTMLDivElement>(null);
  const { outerRatio } = resolveDimensions(document ?? { formatId: "" });
  const size = useFitSize(canvasRef, outerRatio);
  useAutosave();
  useEditorShortcuts();
  useContrastCheck(artboardRef, document, page, size.width);
  useSpaceGuard(artboardRef, size.width);
  useElementFit(artboardRef, size.width);

  return (
    <AppShell
      brand="Layout"
      skipLinkLabel="Zur Arbeitsfläche springen"
      actions={<EditorHeaderActions />}
      controls={<EditorHeaderControls artboardRef={artboardRef} />}
      navigation={<LeftPanel />}
      navigationLabel="Aufbau"
      detail={<Inspector />}
      detailLabel="Eigenschaften"
    >
      <div
        ref={canvasRef}
        className={CANVAS_CLASSES}
        onPointerUp={(event) => event.target === event.currentTarget && select({ type: "none" })}
      >
        {document && page && size.width > 0 && (
          <Artboard
            ref={artboardRef}
            document={document}
            page={page}
            width={size.width}
            height={size.height}
            onBackgroundClick={() => select({ type: "none" })}
          />
        )}
      </div>
    </AppShell>
  );
}
