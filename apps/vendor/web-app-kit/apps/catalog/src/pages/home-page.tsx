import { cn, INTERACTIVE_SURFACE_CLASSES, PageContainer } from "@web-app-kit/frontend/ui";
import { CATALOG_ENTRIES } from "../catalog/entries";
import { PATHS } from "../routing/paths";
import { PageHeader } from "../shared/page-header";

const LINKS = [
  {
    href: PATHS.colors,
    title: "Farben",
    text: "Farbrollen und Grautöne mit gemessenem Kontrast in Hell und Dunkel.",
  },
  { href: PATHS.radii, title: "Radien", text: "Die Radius-Skala der Bausteine." },
  {
    href: PATHS.components,
    title: "Komponenten",
    text: `Alle ${CATALOG_ENTRIES.length} Bausteine mit Import, Varianten, Zuständen, Vorschau.`,
  },
  {
    href: PATHS.status,
    title: "Status",
    text: "Welcher Baustein stabil, in Erprobung oder auslaufend ist.",
  },
];

const LINK_CLASSES = cn(
  INTERACTIVE_SURFACE_CLASSES,
  "flex flex-col gap-1 rounded-md border px-4 py-3",
);

export function HomePage() {
  return (
    <PageContainer size="md" className="flex flex-col gap-8">
      <PageHeader title="Kit-Katalog">
        Alle UI-Bausteine aus @web-app-kit/frontend/ui mit Live-Vorschau. Navigation und Rahmen sind
        neutral, die Vorschauen zeigen die Teamfarben aus brand.css.
      </PageHeader>
      <ul className="flex flex-col gap-3">
        {LINKS.map((link) => (
          <li key={link.href}>
            <a href={link.href} className={LINK_CLASSES}>
              <span className="font-medium">{link.title}</span>
              <span className="text-sm text-muted-foreground">{link.text}</span>
            </a>
          </li>
        ))}
      </ul>
    </PageContainer>
  );
}
