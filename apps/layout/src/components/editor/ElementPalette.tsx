import { PanelSection } from "@/components/controls";
import { ELEMENT_DEFINITIONS } from "@/config/elements";
import { ELEMENT_ICONS } from "@/config/elementIcons";
import { addElement } from "@/services/elementOperations";
import { createElement } from "@/services/factories";
import { targetCell } from "@/store/selectors";
import { selectActivePage, useEditorStore } from "@/store/editorStore";
import type { ElementKind } from "@/types/document";
import { useSpaceStore } from "@/store/spaceStore";
import { TileButton } from "./TileButton";

function insertElement(kind: ElementKind): void {
  const { selection, applyPage, select } = useEditorStore.getState();
  const document = selectActivePage(useEditorStore.getState());
  if (!document) return;
  const address = targetCell(document, selection);
  if (!address) return;
  const element = createElement(kind);
  applyPage((current) => addElement(current, address, element));
  select({ type: "element", ...address, elementId: element.id });
}

export function ElementPalette() {
  const hasSections = useEditorStore(
    (state) => (selectActivePage(state)?.sections.length ?? 0) > 0,
  );
  const placeable = useSpaceStore((state) => state.placeable);
  const isBlocked = (kind: ElementKind) => placeable[kind] === false;
  const hasBlocked = ELEMENT_DEFINITIONS.some((definition) => isBlocked(definition.kind));

  return (
    <PanelSection title="Elemente">
      <p className="text-xs text-muted-foreground">
        Landet in der gewählten Spalte, sonst im letzten Bereich.
      </p>
      {hasBlocked && (
        <p className="text-xs text-muted-foreground" role="status">
          Ausgegraut: In dieser Spalte ist dafür kein Platz mehr.
        </p>
      )}
      <div className="grid grid-cols-3 gap-2">
        {ELEMENT_DEFINITIONS.map((definition) => {
          const Icon = ELEMENT_ICONS[definition.kind];
          return (
            <TileButton
              key={definition.kind}
              disabled={!hasSections || isBlocked(definition.kind)}
              onClick={() => insertElement(definition.kind)}
            >
              <Icon aria-hidden="true" />
              {definition.label}
            </TileButton>
          );
        })}
      </div>
    </PanelSection>
  );
}
