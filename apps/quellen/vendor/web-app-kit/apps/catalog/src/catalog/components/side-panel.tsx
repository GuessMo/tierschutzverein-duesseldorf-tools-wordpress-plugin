import { useRef, useState } from "react";
import { Button, SidePanel } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function SidePanelDefault() {
  const [isOpen, setOpen] = useState(true);
  const triggerRef = useRef<HTMLButtonElement>(null);
  return (
    <div className="flex min-h-56 gap-4">
      <div className="flex-1">
        <Button ref={triggerRef} variant="outline" onClick={() => setOpen(true)}>
          Details öffnen
        </Button>
      </div>
      <SidePanel
        side="right"
        width="sm"
        title="Details"
        open={isOpen}
        onOpenChange={setOpen}
        closeLabel="Details schließen"
        returnFocusRef={triggerRef}
        className="rounded-md border"
      >
        <p className="text-sm text-muted-foreground">Inhalt des Detailbereichs.</p>
      </SidePanel>
    </div>
  );
}

export const sidePanelEntry: CatalogEntry = {
  slug: "side-panel",
  name: "SidePanel",
  group: "layout",
  status: "stable",
  description:
    "Seitlicher Bereich für Navigation (links) oder Details (rechts). Mit title wird er zur " +
    "benannten Gruppe; onOpenChange verlangt closeLabel.",
  exports: ["SidePanel"],
  types: ["SidePanelProps", "SidePanelSide", "SidePanelWidth"],
  variants: [
    {
      key: "default",
      title: "Detail rechts mit Schließen",
      description:
        "width sm 16rem, md 20rem, lg 24rem; returnFocusRef gibt den Fokus an den Auslöser.",
      Preview: SidePanelDefault,
    },
  ],
  states: [
    { name: "Offen", description: "Panel sichtbar, Schließen-Button im Kopf." },
    { name: "Geschlossen", description: "open={false} rendert nichts, Fokus geht zurück." },
  ],
};
