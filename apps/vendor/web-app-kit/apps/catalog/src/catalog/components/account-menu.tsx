import { AccountMenu } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function AccountMenuDefault() {
  return (
    <div className="flex justify-end">
      <AccountMenu
        name="Erika Muster"
        initials="EM"
        label="Konto: Erika Muster"
        logoutLabel="Abmelden"
        onLogout={() => undefined}
      />
    </div>
  );
}

export const accountMenuEntry: CatalogEntry = {
  slug: "account-menu",
  name: "AccountMenu",
  group: "menus",
  status: "beta",
  description:
    "Konto ganz rechts in AppShell-controls: Kreis mit Initialen, Menü mit Name und Abmelden. " +
    "Abmelden ruft onLogout, die App entscheidet über den Weg. Optional.",
  exports: ["AccountMenu"],
  types: ["AccountMenuProps"],
  variants: [
    {
      key: "default",
      title: "Initialen mit Abmelden",
      description: "name, initials, label, logoutLabel und onLogout sind Pflicht-Props.",
      Preview: AccountMenuDefault,
    },
  ],
  states: [
    { name: "Geschlossen", description: "Gefüllter Primär-Button, Fokus als Outline außen." },
    {
      name: "Geöffnet",
      description: "Initialen behalten ihre Farbe, Menü mit Name und Abmelden.",
    },
  ],
  previewMinHeight: "12rem",
};
