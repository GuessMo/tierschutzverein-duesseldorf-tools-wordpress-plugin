# Vorlage, Anpassung, Zustände, DOM-Test

## Vorlage holen

Der Kit hat kein `components.json`: `shadcn add` schreibt hier nichts sinnvoll. Die Vorlage
kommt als JSON direkt aus der Registry, Stil `radix-nova` (wie alle Kit-Bausteine):

```bash
curl -fsS "https://ui.shadcn.com/r/styles/radix-nova/<name>.json" -o /tmp/<name>.json
node -e 'const d=require("/tmp/<name>.json");for(const f of d.files)console.log(f.path+"\n"+f.content)'
node -e 'const d=require("/tmp/<name>.json");console.log(d.dependencies,d.registryDependencies)'
```

- Statt `/tmp` den Scratch-Ordner der Sitzung nutzen; die Vorlage nicht ins Repo legen.
- Nicht `npx shadcn@latest view <name>` als Vorlage nehmen: ohne `components.json` liefert es
  den Stil `new-york-v4`, nicht `radix-nova`. `npx shadcn@latest docs <name>` nennt die
  Doku-Seiten; dort die Radix-Fassung lesen, nicht die Base-UI-Fassung.
- Existiert die URL nicht (HTTP 404), gibt es den Baustein in shadcn nicht: Nutzer fragen,
  ob er aus Radix-Primitives im Kit-Stil gebaut werden soll.
- `registryDependencies` (z. B. `button`) zeigen auf andere Bausteine: im Kit vorhandene
  importieren, fehlende zuerst einzeln ergänzen.
- `dependencies` außer `radix-ui` und `cn` sind neue Pakete: Nutzer fragen, bevor eines als
  Peer-Dependency in `packages/frontend/package.json` kommt.

## Anpassen

| Upstream | Im Kit |
| --- | --- |
| `"use client"` | entfernen |
| `import { cn } from "cn"` bzw. `"@/lib/utils"` | `import { cn } from "./cn.js";` |
| Imports anderer Bausteine `@/components/ui/x` | `./x.js` |
| `IconPlaceholder` oder `lucide-react` | passendes Tabler-Icon aus `@tabler/icons-react` |
| langer `className`-String | Array-Konstante `<NAME>_CLASSES` vor der Funktion, je Zeile ≤ 100 Zeichen, Inhalt unverändert |
| `cva(...)` | bleibt; Basis-Klassen als Array, Varianten unverändert |
| Default-Texte (`"Close"`, `"Toggle"`, `sr-only`-Text) | Prop ohne Default, Pflicht wenn das Element sonst unbenannt ist |
| Semikolons fehlen, doppelte Anführungszeichen | Kit-Stil: Semikolons, doppelte Anführungszeichen |

Zeilenumbruch und Konstanten ändern den Inhalt nicht. Ein Klassenstring wird ergänzt
(Kit-Zustände) oder aus einem Grund unter „Ersetzungen" ersetzt, nie umformuliert.

## Ersetzungen

Erlaubt nur mit einem dieser Gründe; jede Ersetzung im CHANGELOG nennen (alt → neu, Grund):

| Grund | Beispiel |
| --- | --- |
| Fokus unter 3:1 oder auf gefüllter Fläche | Halo `ring-ring/50` → `FILLED_FOCUS_CLASSES` |
| Kontrast oder Sichtbarkeit mit Kit-Tokens | `bg-muted` gleich `--background` → `bg-accent` (direkt tauschen, nicht anhängen) |
| Barrierefreiheit | `aria-hidden` für dekorative Flächen, `motion-reduce:*` für Bewegung |
| Funktionsfehler der Vorlage | Prozentwert ignoriert `max` |

Wo es geht, statt Ersetzung eine Kit-Klasse ergänzen, die `cn` (tailwind-merge) gegen die
Upstream-Klasse auflöst: `dark:data-unchecked:bg-input` schlägt
`dark:data-unchecked:bg-input/80`.

## Neue Abhängigkeit

Nach Freigabe durch den Nutzer, alles im selben Pull Request:

1. `packages/frontend/package.json`: `peerDependencies` (`^x.y.z`), `peerDependenciesMeta`
   `optional: true`, `devDependencies` exakt gepinnt.
2. `apps/catalog/package.json`: `dependencies` exakt gepinnt; `npm install` im Wurzelordner.
3. README, Abschnitt UI-Basis „Abhängigkeiten der App": Installationsbefehl, wofür nötig.
4. CHANGELOG `### Umbau`: Installationszeile plus Prüfbefehl für Tools, die den Baustein nutzen.

## Fremde Bibliothek mit eigenem Theme oder CSS

- Theme aus `useTheme().resolvedTheme` (`ThemeProvider`), nie `next-themes`; die
  Katalog-Vorschau dafür in `ThemeProvider` mit `readPreviewTheme` hüllen.
- Eingespieltes Basis-CSS der Bibliothek liegt außerhalb der Tailwind-Layer und gewinnt
  gegen Utilities (z. B. `outline: 0`, Farben je Theme). Gerendert prüfen; betroffene
  Kit-Klassen mit `!` setzen und im DOM-Test prüfen.
- Kein JSX auf Modulebene (`const ICONS = { x: <Icon /> }`): der Katalog-Test lädt die Datei
  ohne React-JSX-Transform. JSX in einer Funktion erzeugen.

## Zustände

| Element | Kit-Klassen |
| --- | --- |
| Fläche ohne Füllung (ghost, outline, Trigger) | `PRIMARY_SUBTLE_STATE_CLASSES`: Hover `--primary-subtle`, gedrückt/geöffnet/`aria-pressed` Primärrahmen plus Innenring |
| gefüllte Fläche (`bg-primary`, `data-checked`, `data-[state=on]`) | Hover `--primary-hover`, gedrückt `--primary-active`, Fokus `FILLED_FOCUS_CLASSES` bzw. `TOGGLE_ON_FOCUS_CLASSES` (Outline 2 px mit Abstand) |
| Sekundär- und Tertiärfläche | `--secondary-hover`/`--secondary-active`, `--tertiary-hover`/`--tertiary-active` |
| Listenzeile, Kachel, Karte | `INTERACTIVE_SURFACE_CLASSES` aus `interactive-surface.ts` |
| Menü- und Select-Eintrag | `MENU_HIGHLIGHT_CLASSES` aus `menu-classes.ts` |
| Fokus ohne Füllung | `focus-visible:ring-2 focus-visible:ring-ring`, Halo `ring-ring/50` nur mit `focus-visible:border-ring` an einem Element mit Rahmen |
| deaktiviert | `disabled:cursor-not-allowed disabled:opacity-50`; Hover/gedrückt hinter `not-disabled:` |
| gedrückt (Bewegung) | `not-disabled:active:translate-y-px` |

Verboten: `hover:bg-muted`, `hover:bg-accent`, eigene `color-mix`-Hover, `:focus` statt
`:focus-visible`, entfernter Fokusring. Fehlt eine geteilte Klasse, die mindestens zwei
Bausteine brauchen: in `interaction-states.ts` ergänzen und dort testen
(`interaction-states.dom.test.tsx`).

## DOM-Test

Datei `packages/frontend/src/ui/<name>.dom.test.tsx`, läuft mit `npm test`
(`src/ui/*.test.tsx`). Gerüst wie die vorhandenen Tests:

```tsx
import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { <Name> } = await import("./index.js");

test.afterEach(cleanup);
```

Pflicht-Fälle:

| Fall | Prüfung |
| --- | --- |
| Rolle und Name | `screen.getByRole(…, { name: … })` mit dem Label-Prop |
| Zustandsklassen | Liste erwarteter Klassen (hover, active, focus-visible, disabled) in `className`; kein `hover:bg-muted`/`hover:bg-accent` |
| Tastatur | `userEvent` mit Tab, Enter, Leertaste, Pfeiltasten, Escape je nach Muster |
| ARIA-Zustand | `aria-checked`/`aria-pressed`/`aria-expanded`/`data-state` wechselt |
| deaktiviert | keine Reaktion auf Klick und Tastatur, `disabled`-Attribut gesetzt |
| Fokus-Rückgabe | bei Overlays: nach Escape liegt der Fokus wieder auf dem Auslöser |

Vorbilder: `toggle.dom.test.tsx` (Zustandsklassen), `checkbox.dom.test.tsx` (Tastatur,
deaktiviert), `context-menu.dom.test.tsx` (Overlay, Fokus-Rückgabe),
`sonner.dom.test.tsx` (fremde Bibliothek, Timer).

Timer der Komponente (Ausblenden, Entfernen) im Test per
`await act(() => new Promise((resolve) => setTimeout(resolve, ms)))` abwarten; `waitFor` kann
in jsdom hängen bleiben und blockiert dann den ganzen Testlauf.

*Zuletzt aktualisiert: 2026-11-05 (Ersetzungen, neue Abhängigkeit, fremde Bibliothek, Timer)*
