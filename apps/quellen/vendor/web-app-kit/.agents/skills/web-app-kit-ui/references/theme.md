# Theme: Primärfarbe, Abweichungen, Kontrast

## Woher die Farben kommen

| Ebene | Datei / Funktion | Wer ändert |
| --- | --- | --- |
| Neutrale Tokens (Flächen, Text, Rahmen, Hover) | `theme.css` des Kits | nur im Kit-Repository |
| Primärfarbe und Fokusring | `packages/frontend/src/brand.css` des Kits (von `theme.css` importiert) | das Team im eigenen Kit-Repository, gilt für alle Tools |
| Abweichung eines einzelnen Tools | `applyThemeOverrides` im Tool | Tool, mit Kontrastnachweis |
| Instanzfarbe (z. B. Umgebung unterscheiden) | `applyAccentOverride` → nur `--brand` | Tool, per Build-Variable |

`brand.css` setzt für Hell (`:root`) und Dunkel (`.dark`):

| Variable | Wirkt auf |
| --- | --- |
| `--primary` | `Button`/`Badge` `default` (Fläche), `link` (Textfarbe), aktives `Toggle`, Rahmen und Innenring gedrückter Elemente, Check-Icon in Menüs |
| `--primary-foreground` | Text auf `--primary`, `--primary-hover`, `--primary-active` |
| `--primary-hover` | Hover auf Primärflächen, Link-Hover |
| `--primary-active` | gedrückte Primärfläche, gedrückter Link |
| `--primary-subtle` | zarte Fläche: Hover von `ghost`/`outline`/`Toggle` aus, Menü- und Select-Fokus, gedrückt/geöffnet/ausgewählt |
| `--primary-subtle-foreground` | Text auf `--primary-subtle` |
| `--ring` | Fokusring aller Kit-Komponenten (teils mit 50 % Deckkraft) |
| `--sidebar-primary`, `--sidebar-primary-foreground` | Hervorhebungen in `SidePanel`-Flächen |
| `--sidebar-ring` | Fokusring in Panel-Flächen |

`brand.css` liegt im Submodule: im Tool nie bearbeiten. Prüfen, ob die gepinnte Kit-Version
die Datei hat: `ls vendor/web-app-kit/packages/frontend/src/brand.css`. Fehlen die vier
Zustands-Rollen in `brand.css`, greifen die neutralen Werte aus `theme.css`
(`--primary-hover-default` usw.).

## Kontrast-Untergrenzen (WCAG 2.2 AA, Hell und Dunkel)

| Paar | Minimum |
| --- | --- |
| `--primary-foreground` auf `--primary`, `--primary-hover`, `--primary-active` | 4.5:1 |
| `--primary-subtle-foreground`, `--foreground`, `--muted-foreground` auf `--primary-subtle` | 4.5:1 |
| `--primary-hover` als Text auf `--primary-subtle` | 4.5:1 |
| `--primary`, `--primary-hover`, `--primary-active` als Text (`link`) auf `--background`, `--card` | 4.5:1 |
| `--primary`, `--primary-hover`, `--primary-active` als Fläche gegen `--background` | 3:1 |
| `--primary` gegen `--card`, `--popover`, `--primary-subtle` (Rahmen, Ring, Check-Icon) | 3:1 |
| `--ring` (gerendert, inkl. 50 % Deckkraft) gegen `--background`, `--card`, `--popover` | 3:1 |
| `--sidebar-primary-foreground` auf `--sidebar-primary` | 4.5:1 |
| `--sidebar-ring` gegen `--sidebar` | 3:1 |
| `--brand-foreground` auf `--brand` (wenn `bg-brand` mit Text genutzt wird) | 4.5:1 |
| `--brand` als Text oder Rahmen gegen die Fläche darunter | 4.5:1 Text, 3:1 Rahmen |

Messen statt schätzen:

- Gerenderte Farben messen, nicht nur Token-Werte: der Fokusring wird teils mit 50 %
  Deckkraft über die Fläche gelegt. Im Browser per DevTools-Farbwähler (zeigt das
  Kontrastverhältnis) oder per Playwright `getComputedStyle` auslesen und rechnen.
- Ab Kit 0.9.0 exportiert `@web-app-kit/frontend` `contrastRatio(first, second)` (OKLCH oder
  `#rrggbb`). Bei älterem Pin `vendor/web-app-kit/packages/frontend/src/index.ts` prüfen;
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
