import type { ReactNode } from "react";
import { AppShell, AppVersion, ThemeToggle } from "@web-app-kit/frontend/ui";
import { PATHS } from "../routing/paths";
import { CatalogNav } from "./catalog-nav";

const THEME_LABELS = { system: "System", light: "Hell", dark: "Dunkel" };

export function CatalogShell({ path, children }: { path: string; children: ReactNode }) {
  return (
    <AppShell
      brand="Kit-Katalog"
      homeHref={PATHS.home}
      skipLinkLabel="Zum Inhalt springen"
      controls={
        <ThemeToggle
          labels={THEME_LABELS}
          describe={(theme) => `Farbschema des Katalogs: ${theme}, wechseln`}
        />
      }
      navigation={<CatalogNav path={path} />}
      navigationLabel="Katalog-Navigation"
      footer={<AppVersion label="Kit-Version" version={`v${import.meta.env.VITE_KIT_VERSION}`} />}
    >
      {children}
    </AppShell>
  );
}
