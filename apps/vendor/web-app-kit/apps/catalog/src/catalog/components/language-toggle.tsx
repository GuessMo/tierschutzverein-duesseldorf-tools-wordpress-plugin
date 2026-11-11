import { useState } from "react";
import { LanguageToggle } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

const LANGUAGES = [
  { code: "de", label: "Deutsch" },
  { code: "en", label: "Englisch" },
];

function LanguageToggleDefault() {
  const [language, setLanguage] = useState("de");
  return (
    <LanguageToggle
      languages={LANGUAGES}
      value={language}
      onChange={setLanguage}
      describe={(current) => `Sprache: ${current.label}, wechseln`}
    />
  );
}

export const languageToggleEntry: CatalogEntry = {
  slug: "language-toggle",
  name: "LanguageToggle",
  group: "layout",
  status: "stable",
  description:
    "Schaltet zyklisch durch die Sprachen der App und zeigt den aktuellen Sprachcode. " +
    "describe enthält den sichtbaren Code (WCAG 2.5.3, ergänzt ihn sonst selbst).",
  exports: ["LanguageToggle"],
  types: ["LanguageToggleProps", "LanguageOption"],
  variants: [
    {
      key: "default",
      title: "Deutsch und Englisch",
      description: "Steht in controls der AppShell vor dem ThemeToggle.",
      Preview: LanguageToggleDefault,
    },
  ],
  states: [
    { name: "Hover / Gedrückt", description: "Wie Button ghost." },
    { name: "Fokus", description: "Sichtbarer Ring in --ring." },
  ],
};
