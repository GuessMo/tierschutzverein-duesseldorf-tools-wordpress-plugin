import { AppVersion } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function AppVersionDefault() {
  return (
    <div className="text-xs text-muted-foreground">
      <AppVersion label="Version" version="v1.4.0-3-gabc1234" />
    </div>
  );
}

export const appVersionEntry: CatalogEntry = {
  slug: "app-version",
  name: "AppVersion",
  group: "layout",
  status: "stable",
  description:
    "Zeigt den ausgelieferten Stand im footer der AppShell, Quelle git describe --tags zum " +
    "Build-Zeitpunkt.",
  exports: ["AppVersion"],
  types: ["AppVersionProps"],
  variants: [
    {
      key: "default",
      title: "Im Footer",
      description: "label und version kommen aus der App.",
      Preview: AppVersionDefault,
    },
  ],
  states: [{ name: "Statisch", description: "Reine Anzeige, keine Zustände." }],
};
