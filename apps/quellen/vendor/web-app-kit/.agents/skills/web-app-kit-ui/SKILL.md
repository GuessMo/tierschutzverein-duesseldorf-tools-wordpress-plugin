---
name: web-app-kit-ui
description: Baut und prüft Oberflächen in Web-Tools, die web-app-kit nutzen - nur Kit-Komponenten aus @web-app-kit/frontend/ui, Tabler-Icons, Beschriftungen per Props und i18n, Pflicht-Zustände hover/gedrückt/focus-visible/deaktiviert, WCAG 2.2 AA in Hell und Dunkel, Seitenaufbau mit App-Rahmen, PageContainer und SidePanel, Dialoge, Formulare, ESLint-Regel interactive-states, Primärfarbe aus brand.css, Abweichungen per applyThemeOverrides mit Kontrastnachweis. Verwenden bei "Seite/Ansicht/Formular/Dialog/Liste im Tool bauen", "Komponente fürs Kit-Tool", "UI prüfen", "Barrierefreiheit/Kontrast im Tool prüfen", "Farbe/Theme/Primärfarbe ändern", "Lint-Fehler interactive-states". Nicht für das Anlegen eines neuen Tools (web-app-kit-new-tool), nicht für Kit-Updates (web-app-kit-update), nicht für die Entwicklung neuer Bausteine im Kit-Repository selbst und nicht für Oberflächen ohne web-app-kit.
license: MIT
metadata:
  version: "1.0.0"
  abgleich: "2026-11-04 skills.sh+agent-skill.co+aiskill.market+agentskills.io"
---

# web-app-kit: UI bauen

Alle Kit-Tools sehen gleich aus und bedienen sich gleich, weil sie dieselben Bausteine
nutzen. Dieser Skill legt fest, wie Seiten, Dialoge und Formulare aus diesen Bausteinen
entstehen und wie das Ergebnis geprüft wird.

## Commands

| Command | Wirkung |
| --- | --- |
| `how-to` | Kurzanleitung für Leser ohne Vorwissen ausgeben |
| `check [pfad]` | UI eines Tools oder einer Datei gegen die Prüfliste prüfen (Abschnitt „Prüfliste") |
| `theme` | Primärfarbe oder Instanzfarbe ändern, mit Kontrastnachweis (Abschnitt „Theme") |

Bei leerem oder unbekanntem Argument diese Liste ausgeben und stoppen.

## How-To

Bei Aufruf mit `how-to` diese Ansicht ausgeben.

- **Zweck:** Oberflächen in einem Kit-Tool so bauen, dass sie konsistent, per Tastatur
  bedienbar und in Hell und Dunkel gut lesbar sind, ohne eigene Komponenten nachzubauen.
- **Wann:** Du baust oder änderst eine Seite, Liste, Detailansicht, einen Dialog oder ein
  Formular in einem Tool mit `vendor/web-app-kit`, oder willst die UI prüfen.
- **Setup:** Das Tool ist nach Skill `web-app-kit-new-tool` eingerichtet (Kit-Submodule,
  ESLint-Plugin, i18n). Für die Sichtprüfung läuft das Tool lokal im Browser.
- **Beispiel:** „Baue eine Projektliste mit Detailbereich rechts" → Tabelle aus `Table*` in
  `PageContainer`, Auswahl öffnet `SidePanel side="right"`, alle Texte aus i18n; danach
  `check` mit Lint, Tastatur-Durchlauf und Kontrastmessung in beiden Modi.
- **Ergebnis/Verifikation:** `npm run lint` ohne Fehler, `npm run build` grün, Prüfliste mit
  Messwerten (Kontrast Hell/Dunkel, Tastatur, 320px Breite) abgehakt.

## Grundregeln

1. **Ein UI-System.** Nur `@web-app-kit/frontend/ui`. Keine lokalen Kopien
   (`components/ui/`), kein `npx shadcn add` im Tool, keine zweite Bibliothek (MUI, Chakra,
   eigene Radix-Wrapper). Fachliche Komponenten des Tools (z. B. `ProjectList`) sind erlaubt,
   wenn sie aus Kit-Bausteinen zusammengesetzt sind.
2. **Icons nur Tabler** (`@tabler/icons-react`), als Komponente übergeben; dekorative Icons
   `aria-hidden`, Icon-only-Buttons mit `aria-label` aus i18n.
3. **Keine festen Texte.** Sichtbarer Text, `aria-label`, `title`, Platzhalter: alles per
   `t("…")`. Kit-Komponenten haben keine Default-Texte und verlangen Beschriftungen als Props.
4. **Alle Zustände.** Jedes interaktive Element zeigt hover, gedrückt, focus-visible und
   deaktiviert. Kit-Bausteine bringen das mit; eigene Elemente nach
   `references/components.md`, Abschnitt „Zustände".
5. **WCAG 2.2 AA in Hell und Dunkel.** Text 4.5:1, Bedienelemente und Fokus 3:1,
   Zielgröße mindestens 24×24px, alles per Tastatur, Escape schließt Overlays, keine
   Tastaturfalle, Fokus nie verdeckt.
6. **Semantische Tokens.** `bg-background`, `text-muted-foreground`, `border-input`,
   `bg-primary-subtle`. Keine Rohfarben (`bg-blue-500`, Hex), keine `dark:`-Overrides für
   Farben, kein `hover:bg-muted`/`hover:bg-accent`.
7. **Layout-Klassen.** `flex`/`grid` mit `gap-*` statt `space-x-*`/`space-y-*`, `size-*`
   statt `w-* h-*` bei gleichen Maßen, Abstände im 4er/8er-Raster, `truncate` für Überlauf.
8. **Semantik vor ARIA.** Klicks auf `<button>`/`<a>`, nie auf `div`; Überschriften in
   Reihenfolge; Listen als `<ul>`; Tabellen als `Table*`.

## Seitenaufbau

| Bereich | Baustein | Regel |
| --- | --- | --- |
| Rahmen | `AppShell` | genau einer pro App, in einem Layout-Wrapper des Tools |
| Kopfzeile links | `brand`, `actions` | Titel aus i18n, `homeHref="/"`; `actions` für die eine wichtigste globale Aktion |
| Kopfzeile rechts | `controls` | `LanguageToggle`, `ThemeToggle` (in dieser Reihenfolge) |
| Navigation links | `navigation` + `navigationLabel` | `SidePanel side="left"` mit Links; aktive Seite `aria-current="page"` |
| Inhalt | `children` | `PageContainer size="md"` (Formular), `"lg"` (Liste), `"full"` (Arbeitsfläche) |
| Detail rechts | `detail` + `detailLabel` | `SidePanel side="right"` mit `title`, `onOpenChange`, `closeLabel`, `returnFocusRef` |
| Fußzeile | `footer` | `AppVersion` |

Pro Ansicht genau eine primäre Aktion (`Button` Default-Variante), alles andere `outline`,
`ghost` oder `secondary`. Leere Listen bekommen einen Leerzustand mit Text und, wenn sinnvoll,
der Aktion zum Anlegen. Ladezustände mit Text in einer Live-Region (`role="status"`) und
`aria-busy` am Bereich, nicht nur Spinner. Muster: `references/components.md`, „Listen".

Details, Props und Code: `references/components.md`.

## Dialoge

- `DialogTitle` immer (bei Bedarf `sr-only`), `DialogDescription` für Kontext.
- `DialogContent closeLabel={t("common.close")}` (Pflicht, solange der Schließen-Button da ist).
- Escape schließt, Fokus kehrt zum Auslöser zurück: Radix-Verhalten nicht überschreiben.
- Bestätigung destruktiver Aktionen mit `variant="destructive"` und konkretem Verb.

## Formulare

- Jedes Feld: sichtbares `Label` mit `htmlFor` auf die `id` des Feldes.
- Fehler am Feld als Text, verbunden per `aria-describedby`, Feld mit `aria-invalid`.
- Fehler-State hält das Fehlerobjekt oder einen i18n-Schlüssel, nie fertigen Text; übersetzt
  wird beim Rendern, damit ein Sprachwechsel die Meldung mitzieht.
- Eigene Prüfungen speichern den Schlüssel (`setNameErrorKey("items.nameRequired")`), angezeigt
  wird `t(nameErrorKey)`.
- Backend-Fehler: Fehlerobjekt speichern, anzeigen mit `localizeError(error, t)` in
  `<p role="alert">` (Status 404/409/422 per i18n-Schlüssel, sonst `describeError` mit
  `t("errors.generic")`; Helfer in Skill `web-app-kit-new-tool`, `references/frontend.md`).
- Kein `new UserFacingError(t("…"))`: der Text bliebe in der alten Sprache stehen.
- Absenden per `<form onSubmit>` und `Button type="submit"`, Enter funktioniert.

## Fehlender Baustein

Braucht die Oberfläche etwas, das `@web-app-kit/frontend/ui` nicht exportiert (z. B. Tabs,
Checkbox, Switch, Toast):

1. Bestand prüfen: `cat vendor/web-app-kit/packages/frontend/src/ui/index.ts`; prüfen, ob eine
   neuere Kit-Version ihn hat (`bash vendor/web-app-kit/scripts/check-kit-version.sh`, dann
   Skill `web-app-kit-update`).
2. Prüfen, ob vorhandene Bausteine reichen (z. B. `Select` statt Radio-Gruppe, `DropdownMenu`
   für ein Menü mit Aktionen, `ToggleGroup` für eine Werkzeugauswahl).
3. Sonst stoppen und dem Nutzer melden: Name des Bausteins, Einsatzzweck, gewünschte Varianten.
   Der Baustein wird im web-app-kit-Repository ergänzt (shadcn-Basis, Tabler-Icons, Labels per
   Props, Zustände, AA-Nachweis), dann per Kit-Update übernommen.
4. Nicht lokal nachbauen, nicht kopieren, keine andere Bibliothek einbinden, auch nicht
   „vorübergehend".

## Theme

Command `theme`: zuerst `references/team-theme.md` lesen. Der dort festgelegte Team-Standard ist
verbindlich: Tools übernehmen ihn unverändert, eine Abweichung per `applyThemeOverrides` nur mit
ausdrücklicher Freigabe des Teams. Danach klären, was geändert werden soll, und
`references/theme.md` folgen.

| Wunsch | Weg |
| --- | --- |
| Primärfarbe für alle Tools des Teams | `brand.css` im Kit-Repository des Teams ändern (`--primary` samt `--primary-hover`, `--primary-active`, `--primary-subtle`, `--primary-subtle-foreground`), Kontrast aller Paare messen, Kit-Release |
| Ein Tool soll anders aussehen (nur mit Team-Freigabe, siehe `references/team-theme.md`) | `applyThemeOverrides({ "--primary": { light, dark }, … })` in `main.tsx`, alle Paare in beiden Modi messen |
| Instanzen unterscheiden (z. B. Test und Produktion) | `applyAccentOverride(import.meta.env.VITE_ACCENT_OKLCH)`, sichtbar nur über `bg-brand`/`text-brand`/`border-brand` |

Zustandsmodell der Kit-Komponenten: Hover zart (`--primary-subtle`), gedrückt, geöffnet und
ausgewählt mit Primärrahmen plus Innenring auf `--primary-subtle`, aktives `Toggle` voll
(`--primary`, Hover `--primary-hover`, gedrückt `--primary-active`). Fehlen die vier Rollen in
`brand.css`, greifen die neutralen Werte aus `theme.css`.

Kontrast-Minimum: Text 4.5:1, Bedienelemente und Fokusring 3:1, in Hell und Dunkel, gerendert
gemessen. Ohne Messwerte keine Theme-Änderung abschließen.

## Prüfliste

Für `check` alle Punkte abarbeiten und als Tabelle (Punkt, Befehl/Methode, Ergebnis, Messwert)
ausgeben. Nicht Gemessenes als „nicht geprüft" melden, nie als ok.

Automatisch (im Ordner `frontend/`):

1. `npm run lint` ohne Fehler und ohne neue Einträge in `eslint-suppressions.json`.
2. `npm run build` grün.
3. Fremde UI-Quellen: `grep -rnE "lucide-react|@radix-ui/|components/ui/|@mui/|@chakra-ui/" src`
   ohne Treffer.
4. Rohfarben und Dark-Overrides: `grep -rnE "(bg|text|border)-(red|blue|green|gray|slate|zinc)-[0-9]|dark:(bg|text|border)-|#[0-9a-fA-F]{6}" src`
   ohne Treffer (fachliche Tokens gehören in `index.css`).
5. Verbotene Muster: `grep -rnE "hover:bg-(muted|accent)|space-[xy]-" src` ohne Treffer.
6. Feste Texte: `grep -rnE 'aria-label="|title="|placeholder="' src` ohne Literale; JSX-Text
   stichprobenartig lesen.
7. Dialoge: jede `DialogContent` hat `closeLabel` (oder `showCloseButton={false}`) und einen
   `DialogTitle`: `grep -rn "DialogContent" src`.
8. Formulare: jedes `Input`/`Textarea`/`SelectTrigger` hat eine `id` mit passendem
   `Label htmlFor`.

Im Browser (Tool lokal starten, beide Modi per `ThemeToggle`):

9. Tastatur-Durchlauf nur mit Tab, Shift+Tab, Enter, Leertaste, Pfeiltasten, Escape: Skip-Link
   zuerst, alles erreichbar, Fokus immer sichtbar und nicht verdeckt, Dialog und Panel per
   Escape bzw. Schließen-Button zu, Fokus kehrt zurück.
10. Kontrast gemessen (Methode in `references/theme.md`): Text 4.5:1, Bedienelemente und Fokus
    3:1, in Hell und Dunkel; Messwerte notieren.
11. Hover, gedrückt, Fokus, deaktiviert an jedem eigenen interaktiven Element sichtbar.
12. Breite 320px: kein horizontales Scrollen der Seite, Kopfzeile schneidet ab statt umzubrechen,
    Panels stapeln sich.
13. Zoom 200 %: nichts abgeschnitten oder überlappend.
14. Zielgröße: Klickflächen mindestens 24×24px.
15. Sprache umschalten: kein Text bleibt in der alten Sprache, `<html lang>` wechselt; auch
    sichtbare Fehlermeldungen (Feld- und Formularfehler) wechseln mit.

## Referenzen

| Datei | Inhalt |
| --- | --- |
| `references/components.md` | Katalog aller Kit-Bausteine mit Pflicht-Props, Code für Seite, Liste, Dialog, Formular, Werkzeugleiste, interaktive Karten, Zustands-Klassen, ESLint-Regel |
| `references/team-theme.md` | Verbindlicher Theme-Standard des Teams (vom Team gepflegt) |
| `references/theme.md` | `brand.css`, Kontrast-Paare, `applyThemeOverrides`, `applyAccentOverride`, fachliche Tokens |

*Zuletzt aktualisiert: 2026-11-04*
