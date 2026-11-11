import { IconPlus } from "@tabler/icons-react";
import { AppShell, AppVersion, Button, PageContainer, SidePanel } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function AppShellDefault() {
  return (
    <AppShell
      brand="Beispiel-Tool"
      homeHref="#start"
      skipLinkLabel="Zum Inhalt springen"
      actions={
        <Button size="sm">
          <IconPlus data-icon="inline-start" aria-hidden />
          Neu
        </Button>
      }
      navigation={
        <SidePanel side="left" width="sm">
          <p className="px-2 text-sm text-muted-foreground">Navigation</p>
        </SidePanel>
      }
      navigationLabel="Hauptnavigation"
      footer={<AppVersion label="Version" version="v1.4.0" />}
    >
      <PageContainer size="md">
        <h1 className="text-xl font-semibold">Inhalt</h1>
      </PageContainer>
    </AppShell>
  );
}

export const appShellEntry: CatalogEntry = {
  slug: "app-shell",
  name: "AppShell",
  group: "layout",
  status: "stable",
  description:
    "Rahmen jeder App: Kopfzeile mit Titel, Aktionen und Steuerung, optionale Navigation " +
    "links, Detail rechts, Fußzeile. Genau einer pro App; unter 48rem stapeln sich die Bereiche.",
  exports: ["AppShell"],
  types: ["AppShellProps", "AppShellLinkProps"],
  variants: [
    {
      key: "default",
      title: "Kopf, Navigation, Inhalt, Fußzeile",
      description:
        "navigation verlangt navigationLabel, detail verlangt detailLabel; renderLink für " +
        "Router-Links.",
      Preview: AppShellDefault,
    },
  ],
  states: [
    { name: "Skip-Link", description: "Erstes Tab-Ziel, springt in den Hauptbereich." },
    { name: "Titel als Link", description: "Mit homeHref: Hover, Gedrückt und Fokus." },
  ],
  previewMinHeight: "20rem",
};
