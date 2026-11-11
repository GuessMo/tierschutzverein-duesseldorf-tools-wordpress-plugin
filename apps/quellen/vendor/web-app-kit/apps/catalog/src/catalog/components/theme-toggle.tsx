import { ThemeProvider } from "@web-app-kit/frontend";
import { ThemeToggle } from "@web-app-kit/frontend/ui";
import { PREVIEW_THEME_STORAGE_KEY, readPreviewTheme } from "../../preview/preview-theme";
import type { CatalogEntry } from "../catalog.types";

const LABELS = { system: "System", light: "Hell", dark: "Dunkel" };

function ThemeToggleDefault() {
  return (
    <ThemeProvider
      storageKey={PREVIEW_THEME_STORAGE_KEY}
      defaultTheme={readPreviewTheme(window.location.search)}
    >
      <ThemeToggle labels={LABELS} describe={(theme) => `Farbschema: ${theme}, wechseln`} />
    </ThemeProvider>
  );
}

export const themeToggleEntry: CatalogEntry = {
  slug: "theme-toggle",
  name: "ThemeToggle",
  group: "layout",
  status: "stable",
  description:
    "Icon-Button für das Farbschema, Zyklus System → Hell → Dunkel. Braucht den " +
    "ThemeProvider aus @web-app-kit/frontend.",
  exports: ["ThemeToggle"],
  types: ["ThemeToggleProps", "ThemeToggleLabels"],
  variants: [
    {
      key: "default",
      title: "In der Kopfzeile",
      description:
        "labels für system, light, dark; describe formuliert aria-label und title. Ein Klick " +
        "schaltet hier nur diese Vorschau um.",
      Preview: ThemeToggleDefault,
    },
  ],
  states: [
    { name: "Hover / Gedrückt", description: "Wie Button ghost." },
    { name: "Fokus", description: "Sichtbarer Ring in --ring." },
  ],
};
