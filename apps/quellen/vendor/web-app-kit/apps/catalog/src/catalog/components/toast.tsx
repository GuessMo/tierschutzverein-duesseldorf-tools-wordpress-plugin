import { ThemeProvider } from "@web-app-kit/frontend";
import { Button, Toaster, toast } from "@web-app-kit/frontend/ui";
import { PREVIEW_THEME_STORAGE_KEY, readPreviewTheme } from "../../preview/preview-theme";
import type { CatalogEntry } from "../catalog.types";
import { PreviewRow } from "../preview-layout";

function showSaved() {
  toast.success("Entwurf gespeichert", { description: "Alle Änderungen sind übernommen." });
}

function showDeleted() {
  toast("Ebene gelöscht", { action: { label: "Rückgängig", onClick: () => {} } });
}

function showError() {
  toast.error("Speichern fehlgeschlagen", { description: "Keine Verbindung zum Server." });
}

function ToastExamples() {
  return (
    <PreviewRow>
      <Button onClick={showSaved}>Erfolg</Button>
      <Button variant="outline" onClick={showDeleted}>
        Mit Aktion
      </Button>
      <Button variant="outline" onClick={showError}>
        Fehler
      </Button>
      <ThemeProvider
        storageKey={PREVIEW_THEME_STORAGE_KEY}
        defaultTheme={readPreviewTheme(window.location.search)}
      >
        <Toaster closeLabel="Hinweis schließen" regionLabel="Benachrichtigungen" />
      </ThemeProvider>
    </PreviewRow>
  );
}

export const toastEntry: CatalogEntry = {
  slug: "toast",
  name: "Toast (Toaster)",
  group: "overlays",
  status: "stable",
  description:
    "Kurze Rückmeldung nach einer Aktion, ohne den Ablauf zu unterbrechen. Toaster einmal " +
    "pro App; Meldungen per toast(), toast.success(), toast.error() usw.",
  exports: ["Toaster", "toast"],
  types: ["ToasterProps"],
  variants: [
    {
      key: "types",
      title: "Erfolg, mit Aktion, Fehler",
      description:
        "closeLabel und regionLabel sind Pflicht; Farben aus --popover, Aktion als Button. " +
        "Hell/Dunkel folgt dem ThemeProvider.",
      Preview: ToastExamples,
    },
  ],
  states: [
    { name: "Ankündigung", description: "Region mit aria-live polite liest neue Meldungen vor." },
    { name: "Schließen", description: "Ghost-Icon-Button mit closeLabel, 24 px." },
    { name: "Fokus", description: "Toast und Buttons mit Outline bzw. Ring in --ring." },
  ],
  previewMinHeight: "18rem",
};
