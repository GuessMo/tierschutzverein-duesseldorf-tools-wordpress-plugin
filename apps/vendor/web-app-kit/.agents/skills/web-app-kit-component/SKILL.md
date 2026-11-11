---
name: web-app-kit-component
description: Ergänzt einen neuen UI-Baustein im web-app-kit-Repository selbst (packages/frontend/src/ui) - Bestand in ui/index.ts und components.md prüfen, shadcn-Vorlage (Stil radix-nova) aus der Registry holen und Upstream-Klassenstrings unverändert übernehmen, Farben nur über Kit-Rollen in OKLCH, Pflicht-Zustände hover/gedrückt/focus-visible/deaktiviert aus interaction-states.ts, Tabler-Icons, Beschriftungen per Props, DOM-Test, Kontrast in Hell und Dunkel, Export, Doku in components.md und im Kit-Katalog, CHANGELOG-Eintrag, Pull Request. Verwenden bei "neue Komponente in den Kit aufnehmen", "Baustein im Kit ergänzen", "Switch/Tabs/Toast fehlt im Kit, bitte einbauen", "shadcn-Komponente in web-app-kit übernehmen", "Kit-Komponente prüfen, bevor sie released wird". Nicht für Oberflächen in einem Tool (web-app-kit-ui), nicht für das Anlegen eines neuen Tools (web-app-kit-new-tool) und nicht für das Anheben der Kit-Version in einem Tool (web-app-kit-update).
license: MIT
metadata:
  version: "1.2.0"
  abgleich: "2026-11-05 skills.sh+agent-skill.co+aiskill.market+agentskills.io"
---

# web-app-kit: Baustein im Kit ergänzen

Neue Bausteine entstehen nur im Kit-Repository, nie in einem Tool. Jeder Baustein folgt
demselben Muster: shadcn-Vorlage, Kit-Farbrollen, alle Zustände, Beschriftungen per Props,
DOM-Test, Doku, Release. Arbeitsort: Wurzelordner des Kit-Repositorys.

## Commands

| Command | Wirkung |
| --- | --- |
| `how-to` | Kurzanleitung für Leser ohne Vorwissen ausgeben |
| `add <name>` | Neuen Baustein Schritt für Schritt ergänzen (Abschnitt „Ablauf") |
| `check [name]` | Fertigen oder geänderten Baustein gegen die Prüfliste prüfen (Abschnitt „Prüfliste") |

Bei leerem oder unbekanntem Argument diese Liste ausgeben und stoppen.

## How-To

Bei Aufruf mit `how-to` diese Ansicht ausgeben.

- **Zweck:** Einen fehlenden UI-Baustein (z. B. Switch, Tabs, Toast) so in den Kit aufnehmen,
  dass er in allen Tools gleich aussieht, per Tastatur bedienbar ist und die Teamfarben nutzt.
- **Wann:** Ein Tool braucht einen Baustein, den `@web-app-kit/frontend/ui` nicht exportiert,
  und vorhandene Bausteine reichen nicht. Oder ein neuer Kit-Baustein soll vor dem Release
  geprüft werden.
- **Setup:** Checkout des Kit-Repositorys, Node und npm, `npm install` im Wurzelordner,
  Netzzugriff auf `ui.shadcn.com` für die Vorlage. Für die Sichtprüfung ein Tool, das den
  Kit lokal einbindet.
- **Beispiel:** `add switch` → Agent prüft den Bestand, holt die shadcn-Vorlage `switch`,
  baut `switch.tsx` mit Kit-Zuständen und Tabler-Icons, schreibt `switch.dom.test.tsx`,
  exportiert ihn, ergänzt `components.md`, Kit-Katalog und CHANGELOG und bereitet den Pull
  Request vor.
- **Ergebnis/Verifikation:** `npm run typecheck` und `npm test` im Wurzelordner grün, neuer
  DOM-Test dabei, Kontrastwerte in Hell und Dunkel notiert, Prüfliste ohne offenen Punkt.

## Grundregeln

- Nur shadcn-Basis (Stil `radix-nova`, Primitives aus `radix-ui`). Keine zweite Bibliothek,
  keine neue Abhängigkeit ohne Rückfrage beim Nutzer; nach Freigabe Ablauf
  `references/upstream.md`, „Neue Abhängigkeit".
- Upstream-Klassenstrings inhaltlich unverändert (AGENTS.md, Ausnahme zu Regel 16): nur
  umbrechen und in Konstanten auslagern. Kit-Zustände kommen als eigene Klassen dazu.
  Ersetzen nur mit Grund aus `references/upstream.md`, „Ersetzungen" (Fokus, Kontrast oder
  Sichtbarkeit, Barrierefreiheit, Funktionsfehler), jede Ersetzung im CHANGELOG nennen.
- Farben nur über Kit-Rollen: `--primary*`, `--secondary*`, `--tertiary*`, die Status-Rollen
  `--destructive`, `--info`, `--warning` und die neutralen Grautöne aus
  `theme.css`. Keine Rohfarben, keine `dark:`-Farb-Overrides, neue Tokens nur
  als `oklch(L C H)`.
- Icons nur `@tabler/icons-react`, dekorativ mit `aria-hidden`.
- Keine festen Texte: sichtbarer Text, `aria-label`, `title` und Platzhalter kommen per Props;
  für Pflicht-Beschriftungen ein Pflicht-Prop ohne Default.
- Prop-Typen von shadcn-Komponenten bleiben inline, eigene Kit-Typen in `<name>.types.ts`.
- Kein Bruch bestehender Exporte. Muss etwas brechen: Major-Sprung, Umbau-Liste im
  CHANGELOG, vorher den Nutzer fragen.

## Ablauf

Platzhalter: `<name>` = Dateiname in kebab-case (`switch`), `<Name>` = Export (`Switch`).

1. **Bestand prüfen.** `cat packages/frontend/src/ui/index.ts` und
   `.agents/skills/web-app-kit-ui/references/components.md` lesen. Gibt es den Baustein oder
   reicht ein vorhandener (`ToggleGroup` statt Radio-Gruppe, `DropdownMenu` für Aktionen),
   stoppen und das melden. Unklarer Einsatzzweck oder unklare Varianten: beim Nutzer erfragen.
2. **Vorlage holen.** shadcn-Registry, Stil `radix-nova`
   (`references/upstream.md`, „Vorlage holen"). Geteilte Klassen zuerst in
   `interaction-states.ts`, `menu-classes.ts`, `interactive-surface.ts` suchen.
3. **Anpassen.** Datei `packages/frontend/src/ui/<name>.tsx` nach
   `references/upstream.md`, „Anpassen": Imports, Tabler-Icons, Konstanten, Zeilen ≤ 100.
4. **Zustände.** hover, gedrückt, focus-visible, deaktiviert, dazu ausgewählt/geöffnet, wo
   vorhanden. Klassen aus `interaction-states.ts`; gefüllte Flächen (`bg-primary`,
   `data-[state=on]`, `data-checked`) fokussieren mit `FILLED_FOCUS_CLASSES` bzw.
   `TOGGLE_ON_FOCUS_CLASSES`, weil ein Halo auf gleichfarbiger Fläche unter 3:1 bleibt.
   Zuordnung: `references/upstream.md`, „Zustände".
5. **Beschriftungen.** Jede Beschriftung als Prop; Icon-only-Bedienelemente verlangen ein
   Label-Prop. Ein Typ in `<name>.types.ts`, wenn der Baustein eigene Props hat.
6. **DOM-Test.** `packages/frontend/src/ui/<name>.dom.test.tsx` nach
   `references/upstream.md`, „DOM-Test": Rolle und ARIA, Tastatur, deaktiviert,
   Zustandsklassen, Label-Props.
7. **Kontrast.** Neue Farbpaare in `brand.test.ts` (`PAIRS`/`ROLE_PAIRS`) bzw.
   `active-state.test.ts` eintragen. Gerendert in Hell und Dunkel messen (Methode:
   `.agents/skills/web-app-kit-ui/references/theme.md`), auf Seite und auf `--card`, Werte als
   Tabelle für den Pull Request notieren: Text 4.5:1, Bedienelemente und Fokus 3:1.
   Alpha-Flächen (`bg-input/80`) bildet `brand.test.ts` nicht ab: gerendert messen, unter 3:1
   per Kit-Klasse durch die volle Rolle ersetzen (`dark:data-unchecked:bg-input`) und die
   Klasse im DOM-Test prüfen. Zustände mit `transition-*` erst nach Ablauf messen (≥ 300 ms).
8. **Export.** In `packages/frontend/src/ui/index.ts` Komponente, Varianten-Funktion und
   Typen exportieren (`export type { … } from "./<name>.types.js"`), Import-Endung `.js`.
9. **Prüfen.** Im Wurzelordner `npm run typecheck`, `npm test`, `npm run test:scripts`.
10. **Doku.** Eintrag in `.agents/skills/web-app-kit-ui/references/components.md` (Tabelle
    mit Varianten, Pflicht-Props, Regeln, bei Bedarf Muster). Katalog: Eintrag
    `apps/catalog/src/catalog/components/<name>.tsx` anlegen UND in
    `apps/catalog/src/catalog/entries.ts` importieren und in `CATALOG_ENTRIES` aufnehmen;
    Hilfs-Exporte ohne Oberfläche (`*Variants`, `*_CLASSES`) in
    `apps/catalog/src/catalog/export-exceptions.ts` mit Begründung. `npm test` (Katalog-
    Coverage) schlägt sonst fehl. README-Abschnitt zur UI-Basis ergänzen, wenn er Bausteine
    aufzählt. Beispiele fehlender Bausteine in `web-app-kit-ui` nachziehen, wenn sie den neuen
    Baustein nennen.
11. **CHANGELOG und Version.** Nach AGENTS.md, „Versionierung": neuer Baustein = MINOR,
    `App-Aktion: optional` mit Hinweis, welche lokalen Nachbauten er ersetzt. `VERSION`, beide
    `package.json` und Eintrag im selben Pull Request.
12. **Pull Request.** Nur wenn der Auftrag Branch, Commit oder Pull Request umfasst; sonst
    beim Stand im Arbeitsbaum stoppen und berichten. Feature-Branch im Kit-Repository
    (`git switch -c feat/<name> origin/main`), Commit `feat(ui): add <Name>`, Pull Request
    nach dem Git-Ablauf des Teams mit Kontrasttabelle und Prüfliste. Push und Pull Request nur
    nach Rückfrage. Tag erst nach dem Merge mit `scripts/tag-release.sh`.

## Prüfliste

Für `check` jeden Punkt prüfen und als Tabelle (Punkt, Befehl/Methode, Ergebnis) ausgeben.
Nicht Gemessenes als „nicht geprüft" melden, nie als ok. Pfade relativ zum Wurzelordner,
`UI=packages/frontend/src/ui`.

1. Export vorhanden: `grep -n "<Name>" $UI/index.ts`.
2. Keine fremden Icons: `grep -nE "lucide|IconPlaceholder" $UI/<name>.tsx` ohne Treffer.
3. Keine Rohfarben: `grep -nE "(bg|text|border)-(red|blue|green|gray|slate|zinc)-[0-9]|#[0-9a-fA-F]{3,8}\b|rgba?\(|hsla?\(" $UI/<name>.tsx`
   ohne Treffer.
4. Keine festen Texte: `grep -nE 'aria-label="|title="|placeholder="|>[A-Za-zÄÖÜäöü]' $UI/<name>.tsx`
   ohne Literale.
5. Zustände: je eine `hover:`-, `active:`- und `focus-visible:`-Klasse sowie `disabled:` bzw.
   ein Kit-Export aus `interaction-states.ts`; gefüllte Flächen mit Outline-Fokus.
6. Verbotene Muster: `grep -nE "hover:bg-(muted|accent)|space-[xy]-|\"use client\"" $UI/<name>.tsx`
   ohne Treffer.
7. DOM-Test vorhanden und grün: `ls $UI/<name>.dom.test.tsx`, `npm test`.
8. Typen: `npm run typecheck` grün.
9. Kontrast Hell und Dunkel gemessen, Werte notiert; neue Paare in den Kontrasttests.
10. Doku: Eintrag in `components.md`, Katalog-Datei, Import in `entries.ts`, Helfer in
    `export-exceptions.ts`; `npm test -w @web-app-kit/catalog` grün.
11. CHANGELOG-Eintrag mit `App-Aktion:`-Zeile, Versionen gleich (`VERSION`, beide
    `package.json`).
12. Dateien ≤ 200 Zeilen, Zeilen ≤ 100 Zeichen, keine Kommentare:
    `wc -l $UI/<name>*.tsx`, `awk 'length > 100' $UI/<name>*.tsx`. Ausnahme: ein einzelnes
    Upstream-Klassen-Token, das länger als 100 Zeichen ist, steht allein in seiner Zeile und
    wird im Pull Request genannt.
13. Fremde Bibliothek (z. B. `sonner`): Theme aus `useTheme().resolvedTheme`, Basis-CSS der
    Bibliothek überschreibt Tailwind-Klassen nicht still (gerendert geprüft, sonst `!`-Klassen),
    Peer-Dependency nach „Neue Abhängigkeit".

## Referenzen

| Datei | Inhalt |
| --- | --- |
| `references/upstream.md` | Vorlage aus der shadcn-Registry, Anpassungen, Zustands-Zuordnung, DOM-Test-Gerüst |
| `.agents/skills/web-app-kit-ui/references/components.md` | Bestand und Regeln aller Bausteine |
| `.agents/skills/web-app-kit-ui/references/theme.md` | Farbrollen, Kontrastpaare, Messmethode |

*Zuletzt aktualisiert: 2026-11-10 (1.2.0: Status-Rollen info/warning). Zuvor 2026-11-05 (1.1.0: Katalog-Registrierung, Ersetzungen, neue
Abhängigkeit, Alpha-Flächen, fremde Bibliotheken, lange Tokens, Git nur nach Auftrag)*
