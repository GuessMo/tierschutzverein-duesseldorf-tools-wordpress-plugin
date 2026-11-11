# Theme: Primärfarbe, Abweichungen, Kontrast

## Woher die Farben kommen

| Ebene | Datei / Funktion | Wer ändert |
| --- | --- | --- |
| Neutrale Tokens (Flächen, Text, Rahmen, Hover) | `theme.css` des Kits; Farbstich und Einzelwerte in `brand.css` | Farbstich/Einzelwerte: das Team im eigenen Kit-Repository |
| Primär-, Sekundär-, Tertiärfarbe und Fokusring | `packages/frontend/src/brand.css` des Kits (von `theme.css` importiert) | das Team im eigenen Kit-Repository, gilt für alle Tools |
| Abweichung eines einzelnen Tools | `applyThemeOverrides` im Tool | Tool, mit Kontrastnachweis |
| Instanzfarbe (z. B. Umgebung unterscheiden) | `applyAccentOverride` → nur `--brand` | Tool, per Build-Variable |

`brand.css` setzt für Hell (`:root`) und Dunkel (`.dark`):

| Variable | Wirkt auf |
| --- | --- |
| `--primary` | `Button`/`Badge` `default` (Fläche), `link` (Textfarbe), aktives `Toggle`, Rahmen und Innenring gedrückter Elemente, Check-Icon in Menüs |
| `--primary-foreground` | Text auf `--primary`, `--primary-hover`, `--primary-active` |
| `--primary-hover` | Hover auf Primärflächen, Link-Hover |
| `--primary-active` | gedrückte Primärfläche, gedrückter Link |
| `--primary-subtle` | zarte Fläche: Hover von `ghost`/`outline`/`Toggle` aus, Menü- und Select-Fokus (plus Ring in `--ring`), gedrückt/geöffnet/ausgewählt |
| `--primary-subtle-foreground` | Text auf `--primary-subtle` |
| `--ring` | Fokusring aller Kit-Komponenten (Halo mit 50 % Deckkraft nur zusammen mit 1-px-Rahmen in `--ring`), Ring markierter Menü-Einträge, Basis-Outline |
| `--secondary`, `--secondary-foreground` | `Button`/`Badge` `secondary` (Fläche, Text) |
| `--secondary-hover`, `--secondary-active` | Hover und gedrückt von `Button` `secondary` |
| `--secondary-subtle`, `--secondary-subtle-foreground` | zarte Sekundärfläche mit Text, für eigene Flächen im Tool |
| `--tertiary`, `--tertiary-foreground` | `Button` `tertiary` (neutrale Fläche, Text) |
| `--tertiary-hover`, `--tertiary-active` | Hover und gedrückt von `Button` `tertiary` |
| `--tertiary-active-foreground` | Text auf `--tertiary-active` |
| `--sidebar-primary`, `--sidebar-primary-foreground` | Hervorhebungen in `SidePanel`-Flächen |
| `--sidebar-ring` | Fokusring in Panel-Flächen |

`brand.css` liegt im Submodule: im Tool nie bearbeiten. Prüfen, ob die gepinnte Kit-Version
die Datei hat: `ls vendor/web-app-kit/packages/frontend/src/brand.css`. Fehlen die vier
Zustands-Rollen in `brand.css`, greifen die neutralen Werte aus `theme.css`
(`--primary-hover-default` usw.). Ab Kit 0.12.0 gilt das ebenso für die Sekundär- und
Tertiär-Rollen (`--secondary-hover-default`, `--tertiary-default` usw.): ohne eigene Werte
bleibt `secondary` wie bisher, `tertiary` ist die neutrale Graufläche.

Verwendung: `default` (Primär) = Hauptaktion, `secondary` = zweite Aktion, `tertiary` =
neutrale oder untergeordnete Aktion.

Farbwerte in `brand.css` immer als `oklch(L C H)` (außerdem erlaubt: `var(…)`,
`color-mix(in oklch, …)`). Hex, `rgb()` und `hsl()` lässt der Kit-Test `brand.test.ts`
scheitern.

## Grautöne: Farbstich und Einzelwerte (ab Kit 0.13.0)

Alle neutralen Tokens (`--background`, `--foreground`, `--card*`, `--popover*`, `--secondary*`,
`--muted*`, `--accent*`, `--border`, `--input`, `--sidebar*`, `--surface-*`, `--brand*`, neutrale
`--secondary-*`/`--tertiary-*`-Defaults) rechnen sich aus zwei Seeds in `brand.css`:

| Variable | Default | Wirkung |
| --- | --- | --- |
| `--neutral-hue` | `0` | Farbton des Stichs (Grad) |
| `--neutral-chroma` | `0` | Stärke; `0` = reines Grau wie bisher |

Helligkeit je Token bleibt gleich (`oklch(0.97 var(--neutral-chroma) var(--neutral-hue))`).
`--destructive` und `--primary-*-default` bleiben unverändert. Seed unter `:root` gilt für Hell
und Dunkel:

```css
:root {
    --neutral-hue: 60;
    --neutral-chroma: 0.012;
}
```

Einzelwerte: jeden Grauton direkt in `brand.css` setzen, immer in `:root` UND `.dark` (nur
`:root` gälte auch im Dunkelmodus). `theme.css` legt die Grautöne unter `:where(:root)` /
`:where(.dark)` ab (Spezifität 0), daher gewinnt `brand.css`, obwohl `theme.css` sie zuerst
importiert.

Obergrenze (Kit-Test `neutral-tint.test.ts`, jeder Hue, Hell und Dunkel): bis
`--neutral-chroma: 0.09` bestehen alle Text- (4.5:1) und `--input`/`--ring`-Paare (3:1).
Empfohlen für leicht getönte Grautöne: 0.005 bis 0.02. Darüber liegen die hellen Flächen
außerhalb von sRGB und das Grau wirkt farbig. Gemessen bei 0.02 (kühl 250, Deep Pink 350):
`--muted-foreground` mindestens 4.88:1 Hell / 7.01:1 Dunkel, `--input` 3.33 / 3.51.

## Status-Rollen Info und Warnung

`theme.css` setzt wie `--destructive` feste Werte unter `:where(:root)` / `:where(.dark)`
(Spezifität 0): ein Team überschreibt sie in `brand.css`, immer in `:root` UND `.dark`.

| Variable | Hell | Dunkel | Wirkt auf |
| --- | --- | --- | --- |
| `--info` | `oklch(0.45 0.14 250)` | `oklch(0.82 0.09 240)` | `Badge` `info`: Text, getönte Fläche (Hell 10 %, Hover 20 %; Dunkel 20 %, Hover 25 %) |
| `--info-foreground` | `oklch(0.985 0 0)` | `oklch(0.2 0.03 250)` | Text auf voller `--info`-Fläche (`bg-info text-info-foreground`) |
| `--warning` | `oklch(0.47 0.1 70)` | `oklch(0.85 0.12 80)` | `Badge` `warning`, wie `--info` |
| `--warning-foreground` | `oklch(0.985 0 0)` | `oklch(0.2 0.03 70)` | Text auf voller `--warning`-Fläche |

Utilities: `bg-info`, `text-info`, `bg-info/10`, `text-info-foreground`, entsprechend `warning`.
Statt lokaler Klassen wie `bg-[var(--info)]` die `Badge`-Varianten nutzen. Status nie nur über
Farbe zeigen, der Text trägt die Bedeutung.

## Kontrast-Untergrenzen (WCAG 2.2 AA, Hell und Dunkel)

| Paar | Minimum |
| --- | --- |
| `--primary-foreground` auf `--primary`, `--primary-hover`, `--primary-active` | 4.5:1 |
| `--primary-subtle-foreground`, `--foreground`, `--muted-foreground` auf `--primary-subtle` | 4.5:1 |
| `--primary-hover` als Text auf `--primary-subtle` | 4.5:1 |
| `--primary`, `--primary-hover`, `--primary-active` als Text (`link`) auf `--background`, `--card` | 4.5:1 |
| `--primary`, `--primary-hover`, `--primary-active` als Fläche gegen `--background` | 3:1 |
| `--primary` gegen `--card`, `--popover`, `--primary-subtle` (Rahmen, Ring, Check-Icon) | 3:1 |
| `--ring` (voll deckend) gegen `--background`, `--card`, `--popover`, `--sidebar`, `--primary-subtle` | 3:1 |
| `--ring` mit 50 % Deckkraft (Halo an Elementen mit Rahmen) gegen `--background` | 3:1, sonst nur mit Rahmen in `--ring` |
| `--secondary-foreground` auf `--secondary`, `--secondary-hover`, `--secondary-active` | 4.5:1 |
| `--secondary-subtle-foreground` auf `--secondary-subtle` | 4.5:1 |
| `--secondary` als Fläche gegen `--background` | 3:1 |
| `--tertiary-foreground` auf `--tertiary`, `--tertiary-hover` | 4.5:1 |
| `--tertiary-active-foreground` auf `--tertiary-active` | 4.5:1 |
| `--sidebar-primary-foreground` auf `--sidebar-primary` | 4.5:1 |
| `--sidebar-ring` gegen `--sidebar` | 3:1 |
| `--info`, `--warning` als Text auf `--background`, `--card` und auf der eigenen getönten Fläche (`/10`, `/20`, `/25`) | 4.5:1 |
| `--info-foreground` auf `--info`, `--warning-foreground` auf `--warning` | 4.5:1 |
| `--brand-foreground` auf `--brand` (wenn `bg-brand` mit Text genutzt wird) | 4.5:1 |
| `--brand` als Text oder Rahmen gegen die Fläche darunter | 4.5:1 Text, 3:1 Rahmen |

Messen statt schätzen:

- Gerenderte Farben messen, nicht nur Token-Werte: der Fokusring wird teils mit 50 %
  Deckkraft über die Fläche gelegt. Im Browser per DevTools-Farbwähler (zeigt das
  Kontrastverhältnis) oder per Playwright `getComputedStyle` auslesen und rechnen.
- Ab Kit 0.9.0 exportiert `@web-app-kit/frontend` `contrastRatio(first, second)` (Werte als
  OKLCH). Bei älterem Pin `vendor/web-app-kit/packages/frontend/src/index.ts` prüfen;
  fehlt der Export, Pin anheben (Skill `web-app-kit-update`).
- Beide Modi prüfen: System-Modus und per `ThemeToggle` gesetzte Klasse.
- Ergebnis als Tabelle festhalten (Paar, Hell, Dunkel), z. B. im Pull Request.

## Abweichung für ein Tool: `applyThemeOverrides`

Nur wenn ein Tool bewusst von der Team-Primärfarbe abweicht. Immer Werte für Hell und Dunkel:

```ts
import { applyThemeOverrides } from "@web-app-kit/frontend";

applyThemeOverrides({
  "--primary": { light: "oklch(0.38 0.14 250)", dark: "oklch(0.8 0.11 250)" },
  "--primary-foreground": { light: "oklch(0.985 0 0)", dark: "oklch(0.2 0.03 250)" },
  "--ring": { light: "oklch(0.25 0.1 250)", dark: "oklch(0.85 0.08 250)" },
});
```

Gerechnet gegen die Kit-Flächen: Text auf Primär 9.4 (Hell) / 9.7 (Dunkel), Text auf Hover
5.6 / 6.6, Fokusring mit 50 % Deckkraft 3.2 / 3.5. Der Ring braucht in Hell einen deutlich
dunkleren Wert als die Primärfläche: mit dem Primärwert selbst ergäbe er nur 2.6. Im Tool
trotzdem gerendert nachmessen.

- Aufruf in `main.tsx` vor `createRoot(...).render`.
- Zusammengehörige Variablen gemeinsam überschreiben (`--primary` mit `--primary-foreground`
  und `--ring`; Panel-Varianten mit `--sidebar-*`), sonst passen die Paare nicht mehr.
- Jedes Paar aus der Tabelle oben in beiden Modi messen. Ein Wert unter dem Minimum: Helligkeit
  (L in OKLCH) anpassen, erneut messen. Ohne Nachweis nicht übernehmen.
- Ein einzelner Inline-Wert für beide Modi ist nicht möglich (der Typ verlangt `light` und
  `dark`): eine Farbe erfüllt AA nicht auf hellem und dunklem Grund zugleich.

## Instanzfarbe: `applyAccentOverride`

```ts
applyAccentOverride(import.meta.env.VITE_ACCENT_OKLCH);
```

- Setzt nur `--brand`. Fokus, Hover und Menü-Items bleiben unverändert (AA bleibt erhalten).
- Sichtbar nur dort, wo das Tool `bg-brand`, `text-brand` oder `border-brand` einsetzt (z. B.
  schmale Leiste, Logo). `--brand-foreground` passt sich nicht an: Text auf `bg-brand` selbst
  messen.
- Ohne Variable passiert nichts; `--brand` ist dann neutral wie `--primary`.

## Eigene fachliche Tokens

Für Fachliches (Diagrammreihen, Status) eigene Variablen in `src/index.css` unter `:root` und
`.dark` definieren und per `@theme inline` als Utility verfügbar machen:

```css
@theme inline {
  --color-status-active: var(--status-active);
}

:root {
  --status-active: oklch(0.5 0.13 150);
}

.dark {
  --status-active: oklch(0.78 0.13 150);
}
```

Gleiche Kontrastregeln. Status nie nur über Farbe zeigen, immer zusätzlich Text oder Icon.
