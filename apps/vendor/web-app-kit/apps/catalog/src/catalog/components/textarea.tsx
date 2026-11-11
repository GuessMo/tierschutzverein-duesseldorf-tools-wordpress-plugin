import { Label, Textarea } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

function TextareaDefault() {
  return (
    <PreviewStack>
      <div className="flex flex-col gap-2">
        <Label htmlFor="textarea-note">Notiz</Label>
        <Textarea id="textarea-note" placeholder="Was soll das Team wissen?" />
      </div>
      <div className="flex flex-col gap-2">
        <Label htmlFor="textarea-disabled">Freigabe-Kommentar</Label>
        <Textarea id="textarea-disabled" defaultValue="Freigegeben am 3. November." disabled />
      </div>
    </PreviewStack>
  );
}

export const textareaEntry: CatalogEntry = {
  slug: "textarea",
  name: "Textarea",
  group: "forms",
  status: "stable",
  description: "Mehrzeiliges Eingabefeld, wächst mit dem Inhalt. Regeln wie bei Input.",
  exports: ["Textarea"],
  variants: [
    {
      key: "default",
      title: "Standard und deaktiviert",
      description: "Mit sichtbarem Label; Platzhalter nur als Beispieltext.",
      Preview: TextareaDefault,
    },
  ],
  states: [
    { name: "Fokus", description: "Rahmen und Ring in --ring." },
    { name: "Fehler", description: "aria-invalid färbt Rahmen und Ring in --destructive." },
    { name: "Deaktiviert", description: "50 % Deckkraft, nicht editierbar." },
  ],
};
