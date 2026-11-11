import { useState } from "react";
import { IconBold, IconGridDots, IconItalic } from "@tabler/icons-react";
import { Toggle } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewRow } from "../preview-layout";

function ToggleVariants() {
  const [isGridVisible, setGridVisible] = useState(true);
  return (
    <PreviewRow>
      <Toggle aria-label="Fett">
        <IconBold aria-hidden />
      </Toggle>
      <Toggle variant="outline" defaultPressed aria-label="Kursiv">
        <IconItalic aria-hidden />
      </Toggle>
      <Toggle variant="outline" pressed={isGridVisible} onPressedChange={setGridVisible}>
        <IconGridDots data-icon="inline-start" aria-hidden />
        Raster
      </Toggle>
      <Toggle variant="outline" disabled aria-label="Fett (deaktiviert)">
        <IconBold aria-hidden />
      </Toggle>
    </PreviewRow>
  );
}

export const toggleEntry: CatalogEntry = {
  slug: "toggle",
  name: "Toggle",
  group: "toolbars",
  status: "stable",
  description:
    "Einzelner An/Aus-Schalter (pressed, onPressedChange). Aktiv trägt die Primärfarbe; " +
    "Icon-only braucht aria-label.",
  exports: ["Toggle"],
  variants: [
    {
      key: "variants",
      title: "default, outline, mit Text, deaktiviert",
      description: "Größen wie bei Button (default, xs, sm, lg, icon …).",
      Preview: ToggleVariants,
    },
  ],
  states: [
    { name: "Hover (aus)", description: "Zarte Primärfläche --primary-subtle." },
    { name: "Gedrückt (aus)", description: "Primärrahmen plus Innenring." },
    {
      name: "An",
      description: "Fläche --primary, Hover --primary-hover, gedrückt --primary-active.",
    },
    { name: "Fokus", description: "Rahmen und Halo in --ring, aktiv als Outline mit Abstand." },
    { name: "Deaktiviert", description: "50 % Deckkraft, nicht schaltbar." },
  ],
};
