# Frontend (Vite + React + Tailwind v4 + Kit)

Ordner `frontend/`. Platzhalter: `<tool>`, `<Tool-Name>` (Anzeigename), `<BACKEND_PORT>`,
`<FRONTEND_PORT>`. UI-Regeln beim Bauen von Seiten: Skill `web-app-kit-ui`.

## Dateien

```
frontend/
├── .npmrc
├── package.json
├── tsconfig.json
├── vite.config.ts
├── eslint.config.js
├── index.html
├── public/favicon.svg
└── src/
    ├── main.tsx
    ├── App.tsx
    ├── index.css
    ├── vite-env.d.ts
    ├── api/{client.ts,errorMessage.ts}
    ├── i18n/{de.ts,en.ts,types.ts,LanguageProvider.tsx,index.ts}
    └── components/layout/{ToolLayout.tsx,LocalizedThemeToggle.tsx,LocalizedLanguageToggle.tsx}
```

Keine `components/ui/`-Kopien und kein `components.json`: alle Bausteine kommen aus
`@web-app-kit/frontend/ui`.

## `.npmrc`

```ini
install-links=true
```

## `package.json`

```json
{
  "name": "<tool>-frontend",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite --host 0.0.0.0",
    "build": "tsc -b && vite build",
    "lint": "eslint .",
    "preview": "vite preview"
  },
  "dependencies": {
    "@fontsource-variable/noto-sans": "5.3.0",
    "@tabler/icons-react": "3.49.0",
    "@tailwindcss/vite": "4.3.2",
    "@web-app-kit/frontend": "file:../vendor/web-app-kit/packages/frontend",
    "class-variance-authority": "0.7.1",
    "clsx": "2.1.1",
    "radix-ui": "1.6.2",
    "react": "19.2.7",
    "react-dom": "19.2.7",
    "shadcn": "4.13.0",
    "tailwind-merge": "3.6.0",
    "tailwindcss": "4.3.2",
    "tw-animate-css": "1.4.0"
  },
  "devDependencies": {
    "@types/node": "26.1.1",
    "@types/react": "19.2.17",
    "@types/react-dom": "19.2.3",
    "@vitejs/plugin-react": "6.0.3",
    "@web-app-kit/eslint-plugin": "file:../vendor/web-app-kit/packages/eslint-plugin",
    "eslint": "9.39.5",
    "typescript": "7.0.2",
    "vite": "8.1.4"
  }
}
```

Versionen exakt so übernehmen; anheben nur gemeinsam per Kit-Release (Grundregel in
`SKILL.md`). `shadcn` wird nur für `shadcn/tailwind.css` gebraucht (Animations-Varianten), nicht
für die CLI. Das ESLint-Plugin verlangt Node `^22.18.0 || >=24.11.0`. Eine npm-Warnung zu
Install-Skripten (z. B. `esbuild`, `fsevents`) beim `npm install` ist harmlos.

## `tsconfig.json`

```json
{
  "compilerOptions": {
    "target": "ES2023",
    "lib": ["ES2023", "DOM", "DOM.Iterable"],
    "module": "ESNext",
    "moduleResolution": "Bundler",
    "jsx": "react-jsx",
    "strict": true,
    "esModuleInterop": true,
    "skipLibCheck": true,
    "resolveJsonModule": true,
    "noEmit": true,
    "paths": { "@/*": ["./src/*"] }
  },
  "include": ["src"]
}
```

## `vite.config.ts`

```ts
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import path from "path";
import { themeInitScript } from "./node_modules/@web-app-kit/frontend/src/themeInitScript.ts";

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    {
      name: "theme-init",
      transformIndexHtml: () => [
        {
          tag: "script",
          children: themeInitScript("<tool>-theme", "system"),
          injectTo: "head-prepend",
        },
      ],
    },
  ],
  resolve: { alias: { "@": path.resolve(__dirname, "./src") } },
  server: { host: "0.0.0.0", port: <FRONTEND_PORT>, watch: { usePolling: true } },
});
```

- Der relative Importpfad mit `.ts` ist Absicht: der Kit liefert TypeScript-Quellen, Vite
  bündelt die Config-Importe.
- `themeInitScript` verhindert das kurze Aufblitzen des hellen Themes. `storageKey` und
  `defaultTheme` müssen exakt zum `ThemeProvider` in `main.tsx` passen.

## `eslint.config.js` (Pflicht)

```js
import webAppKit from "@web-app-kit/eslint-plugin";

export default [...webAppKit.configs.recommended];
```

Aktiviert `web-app-kit/interactive-states`, `web-app-kit/no-foreign-ui` und vier
`jsx-a11y`-Regeln als Fehler. Neues Tool:
von Anfang an ohne Verstöße, keine `eslint-suppressions.json`.

## `index.html`

```html
<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <title><Tool-Name></title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>
```

## Favicon

Im Wurzelordner des Tools erzeugen, Kürzel bzw. Icon beim Nutzer erfragen:

```bash
node vendor/web-app-kit/scripts/create-favicon.mjs --text <1-2 Zeichen>
node vendor/web-app-kit/scripts/create-favicon.mjs \
  --icon frontend/node_modules/@tabler/icons/icons/outline/<icon-name>.svg
```

Schreibt `frontend/public/favicon.svg`: Kachel in `--primary`, Zeichen in
`--primary-foreground`, `oklch`-Werte aus `brand.css` des Kits fest eingesetzt, Hell/Dunkel per
`prefers-color-scheme` im SVG. Die Datei wird committet; nach einem Kit-Update mit geänderter
Primärfarbe neu erzeugen. Details: README des Kits, Abschnitt „Favicon".

## `src/index.css`

```css
@import "tailwindcss";
@import "tw-animate-css";
@import "shadcn/tailwind.css";
@import "@fontsource-variable/noto-sans";
@import "@web-app-kit/frontend/theme.css";
@source "../node_modules/@web-app-kit/frontend/src";

@layer base {
  button:not(:disabled) {
    cursor: pointer;
  }
}
```

- `@source` ist Pflicht. Ohne fehlen die Kit-Klassen im Production-Build (im Dev-Server fällt
  es nicht auf).
- Noto Sans kommt lokal aus `@fontsource-variable/noto-sans`, keine Google- oder CDN-Abfrage.
- Tailwind v4 setzt `button { cursor: default }`; die Regel oben gibt Buttons den Zeiger zurück.
- Eigene Tokens nur für Fachliches (z. B. Diagrammfarben) in `:root` und `.dark`, je mit
  Kontrastnachweis. Primärfarbe und Fokusring nicht hier überschreiben (Skill `web-app-kit-ui`).

## `src/vite-env.d.ts`

```ts
/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_BACKEND_URL?: string;
  readonly VITE_APP_VERSION?: string;
  readonly VITE_ACCENT_OKLCH?: string;
}
```

## `src/main.tsx`

```tsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { applyAccentOverride, ThemeProvider } from "@web-app-kit/frontend";
import { LanguageProvider } from "@/i18n";
import App from "./App";
import "./index.css";

applyAccentOverride(import.meta.env.VITE_ACCENT_OKLCH);

createRoot(document.getElementById("root")!).render(
  <StrictMode>
    <ThemeProvider defaultTheme="system" storageKey="<tool>-theme">
      <LanguageProvider storageKey="<tool>-language">
        <App />
      </LanguageProvider>
    </ThemeProvider>
  </StrictMode>,
);
```

`applyAccentOverride` setzt nur `--brand` (optionale Instanzfarbe, sichtbar nur bei
`bg-brand`/`text-brand`/`border-brand`). Ohne `VITE_ACCENT_OKLCH` passiert nichts.

## i18n (Pflicht, keine fest eingebauten Texte)

Jeder sichtbare Text, jedes `aria-label`, jeder `title` kommt aus den Übersetzungen. Die Kit-
Komponenten haben keine Default-Texte und verlangen Beschriftungen per Props.

`src/i18n/de.ts` (Referenzsprache, legt die Schlüssel fest):

```ts
export const de = {
  "app.title": "<Tool-Name>",
  "layout.skipToContent": "Zum Inhalt springen",
  "layout.version": "Version",
  "layout.versionUnknown": "lokal",
  "layout.kitVersion": "Kit",
  "theme.system": "System",
  "theme.light": "Hell",
  "theme.dark": "Dunkel",
  "theme.toggle": "Darstellung: {label} – klicken zum Wechseln",
  "language.de": "Deutsch",
  "language.en": "Englisch",
  "language.toggle": "Sprache: {label} ({code})",
  "errors.generic": "Etwas ist schiefgelaufen. Bitte erneut versuchen.",
  "errors.notFound": "Der Eintrag wurde nicht gefunden.",
  "errors.conflict": "Der Eintrag wurde inzwischen geändert. Bitte neu laden.",
  "errors.invalid": "Die Eingabe ist ungültig. Bitte prüfen.",
  "common.close": "Schließen",
};
```

`src/i18n/types.ts`:

```ts
import type { de } from "./de";

export type TranslationKey = keyof typeof de;
export type Language = "de" | "en";
export type TranslationValues = Record<string, string | number>;
export type Translate = (key: TranslationKey, values?: TranslationValues) => string;
export type StatusMessageKeys = Partial<Record<number, TranslationKey>>;

export interface LanguageState {
  language: Language;
  setLanguage: (language: Language) => void;
  t: Translate;
}
```

`src/i18n/en.ts` (der Typ erzwingt dieselben Schlüssel):

```ts
import type { TranslationKey } from "./types";

export const en: Record<TranslationKey, string> = {
  "app.title": "<Tool-Name>",
  "layout.skipToContent": "Skip to content",
  "layout.version": "Version",
  "layout.versionUnknown": "local",
  "layout.kitVersion": "Kit",
  "theme.system": "System",
  "theme.light": "Light",
  "theme.dark": "Dark",
  "theme.toggle": "Appearance: {label} – click to change",
  "language.de": "German",
  "language.en": "English",
  "language.toggle": "Language: {label} ({code})",
  "errors.generic": "Something went wrong. Please try again.",
  "errors.notFound": "The entry was not found.",
  "errors.conflict": "The entry has changed in the meantime. Please reload.",
  "errors.invalid": "The input is invalid. Please check it.",
  "common.close": "Close",
};
```

`src/i18n/LanguageProvider.tsx`:

```tsx
import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { de } from "./de";
import { en } from "./en";
import type { Language, LanguageState, TranslationKey, TranslationValues } from "./types";

const TRANSLATIONS = { de, en };

function isLanguage(value: unknown): value is Language {
  return value === "de" || value === "en";
}

function readStoredLanguage(storageKey: string): Language {
  const detected: Language = navigator.language.startsWith("de") ? "de" : "en";
  try {
    const stored = localStorage.getItem(storageKey);
    return isLanguage(stored) ? stored : detected;
  } catch {
    return detected;
  }
}

function persistLanguage(storageKey: string, language: Language): boolean {
  try {
    localStorage.setItem(storageKey, language);
    return true;
  } catch {
    return false;
  }
}

function format(template: string, values?: TranslationValues): string {
  if (!values) return template;
  return Object.entries(values).reduce(
    (text, [name, value]) => text.replaceAll(`{${name}}`, String(value)),
    template,
  );
}

const LanguageContext = createContext<LanguageState | null>(null);

export function LanguageProvider({
  storageKey,
  children,
}: {
  storageKey: string;
  children: ReactNode;
}) {
  const [language, setLanguageState] = useState<Language>(() => readStoredLanguage(storageKey));

  useEffect(() => {
    document.documentElement.lang = language;
  }, [language]);

  const setLanguage = useCallback(
    (next: Language) => {
      setLanguageState(next);
      persistLanguage(storageKey, next);
    },
    [storageKey],
  );
  const t = useCallback(
    (key: TranslationKey, values?: TranslationValues) =>
      format(TRANSLATIONS[language][key], values),
    [language],
  );
  const state = useMemo(() => ({ language, setLanguage, t }), [language, setLanguage, t]);

  return <LanguageContext value={state}>{children}</LanguageContext>;
}

export function useLanguage(): LanguageState {
  const state = useContext(LanguageContext);
  if (!state) throw new Error("useLanguage must be used inside LanguageProvider");
  return state;
}
```

`t` ist per `useCallback` stabil und ändert sich nur mit der Sprache, der Kontextwert per
`useMemo` nur mit Sprache, `setLanguage` oder `t`. So rendern Verbraucher nicht bei jedem
Provider-Render neu, und `t` darf in Abhängigkeitslisten von `useEffect`/`useMemo` stehen.

`src/i18n/index.ts`:

```ts
export { LanguageProvider, useLanguage } from "./LanguageProvider";
export type { Language, StatusMessageKeys, Translate, TranslationKey } from "./types";
```

Neue Texte: Schlüssel zuerst in `de.ts`, dann in `en.ts` (sonst Typfehler).

## Layout: App-Rahmen mit Toggles und Version

`src/components/layout/LocalizedThemeToggle.tsx`:

```tsx
import { ThemeToggle } from "@web-app-kit/frontend/ui";
import { useLanguage } from "@/i18n";

export function LocalizedThemeToggle() {
  const { t } = useLanguage();
  return (
    <ThemeToggle
      labels={{ system: t("theme.system"), light: t("theme.light"), dark: t("theme.dark") }}
      describe={(label) => t("theme.toggle", { label })}
    />
  );
}
```

`src/components/layout/LocalizedLanguageToggle.tsx`:

```tsx
import { LanguageToggle } from "@web-app-kit/frontend/ui";
import { useLanguage } from "@/i18n";

export function LocalizedLanguageToggle() {
  const { language, setLanguage, t } = useLanguage();
  const languages = [
    { code: "de", label: t("language.de") },
    { code: "en", label: t("language.en") },
  ];
  return (
    <LanguageToggle
      languages={languages}
      value={language}
      onChange={(code) => setLanguage(code === "de" ? "de" : "en")}
      describe={({ code, label }) => t("language.toggle", { label, code: code.toUpperCase() })}
    />
  );
}
```

`src/components/layout/ToolLayout.tsx`:

```tsx
import type { ReactNode } from "react";
import { AppShell, AppVersion } from "@web-app-kit/frontend/ui";
import { useLanguage } from "@/i18n";
import { LocalizedLanguageToggle } from "./LocalizedLanguageToggle";
import { LocalizedThemeToggle } from "./LocalizedThemeToggle";

export function ToolLayout({ children }: { children: ReactNode }) {
  const { t } = useLanguage();
  const version = import.meta.env.VITE_APP_VERSION || t("layout.versionUnknown");
  return (
    <AppShell
      brand={t("app.title")}
      homeHref="/"
      skipLinkLabel={t("layout.skipToContent")}
      controls={
        <>
          <LocalizedLanguageToggle />
          <LocalizedThemeToggle />
        </>
      }
      footer={
        <AppVersion
          label={t("layout.version")}
          version={version}
          kitLabel={t("layout.kitVersion")}
        />
      }
    >
      {children}
    </AppShell>
  );
}
```

Props-Typen dieser drei Dateien bei Wachstum in `*.types.ts` auslagern. Navigation links,
Detailbereich rechts, Seitenbreite: Skill `web-app-kit-ui`.

`src/App.tsx`:

```tsx
import { PageContainer } from "@web-app-kit/frontend/ui";
import { ToolLayout } from "@/components/layout/ToolLayout";

export default function App() {
  return (
    <ToolLayout>
      <PageContainer size="lg">{/* erste Seite */}</PageContainer>
    </ToolLayout>
  );
}
```

## API-Client und Fehlermeldungen

`src/api/client.ts`:

```ts
import { createApiClient } from "@web-app-kit/frontend";

const DEV_BACKEND_URL = "http://localhost:<BACKEND_PORT>";
const devFallbackUrl = import.meta.env.DEV ? DEV_BACKEND_URL : "";

export const backendUrl = import.meta.env.VITE_BACKEND_URL ?? devFallbackUrl;
export const api = createApiClient(`${backendUrl}/api`);
```

Im Container liefert das Backend das Frontend selbst aus; `backendUrl` ist dann leer und
Anfragen gehen an `/api` derselben Herkunft.

### Fehlermeldungen i18n-sicher

Regel: Der Fehler-State hält das Fehlerobjekt (`unknown`) oder einen i18n-Schlüssel, nie einen
fertigen Text. Übersetzt wird erst beim Rendern; ein Sprachwechsel zieht die Meldung mit
(Prüfpunkt 15 in `web-app-kit-ui`).

| Fehlerart | State | Anzeige beim Rendern |
| --- | --- | --- |
| eigene Prüfung im Frontend | `TranslationKey` | `t(key)` |
| Antwort des Backends, erwarteter Status (404, 409, 422) | Fehlerobjekt | `localizeError(error, t)` liefert den Text zum Status |
| Netzwerk-, Parse-, Programmfehler, 5xx | Fehlerobjekt | `localizeError` liefert `t("errors.generic")` |
| Backend-Antwort mit nicht zugeordnetem 4xx-Status | Fehlerobjekt | `describeError` zeigt die englische Backend-Meldung als letzten Fallback |

Die Backend-Meldung ist Englisch und für Log und Fallback gedacht. Jeden Status, den eine Route
erwartbar liefert, einem Schlüssel zuordnen, damit der Fallback praktisch nie greift.

`src/api/errorMessage.ts`:

```ts
import { ApiError, describeError } from "@web-app-kit/frontend";
import type { StatusMessageKeys, Translate, TranslationKey } from "@/i18n";

const DEFAULT_STATUS_KEYS: StatusMessageKeys = {
  404: "errors.notFound",
  409: "errors.conflict",
  422: "errors.invalid",
};

function statusKey(status: number, statusKeys: StatusMessageKeys): TranslationKey | undefined {
  const genericKey = status >= 500 ? "errors.generic" : undefined;
  return statusKeys[status] ?? DEFAULT_STATUS_KEYS[status] ?? genericKey;
}

export function localizeError(
  error: unknown,
  t: Translate,
  statusKeys: StatusMessageKeys = {},
): string {
  const key = error instanceof ApiError ? statusKey(error.status, statusKeys) : undefined;
  return key ? t(key) : describeError(error, t("errors.generic"));
}
```

Aufruf (Speichern mit eigener Prüfung und Backend-Fehler):

```tsx
const { t } = useLanguage();
const [nameErrorKey, setNameErrorKey] = useState<TranslationKey | null>(null);
const [submitError, setSubmitError] = useState<unknown>(null);

const save = async () => {
  setSubmitError(null);
  if (!name.trim()) {
    setNameErrorKey("items.nameRequired");
    return;
  }
  setNameErrorKey(null);
  try {
    await api.request<Item>("/items", { method: "POST", body: JSON.stringify({ name }) });
  } catch (error) {
    setSubmitError(error);
  }
};

const submitMessage =
  submitError === null ? null : localizeError(submitError, t, { 409: "items.nameTaken" });
```

- Route-spezifische Texte per drittem Argument (`{ 409: "items.nameTaken" }`), sonst gelten die
  Standard-Schlüssel `errors.notFound`, `errors.conflict`, `errors.invalid`.
- `UserFacingError` nur für Texte, die beim Rendern nicht mehr übersetzt werden müssen
  (sprachunabhängig, z. B. ein Dateiname). Nie `new UserFacingError(t("…"))`: der Text bliebe
  in der Sprache des Fehlerzeitpunkts stehen.
- `describeError` allein zeigt Meldungen von `ApiError` und `UserFacingError` ungefiltert, alles
  andere ergibt den Fallback; im UI deshalb immer `localizeError`.
- Fehler als Text anzeigen, z. B. `<p role="alert" className="text-destructive">`, nie nur
  über Farbe. Formular-Muster mit Feldfehler: Skill `web-app-kit-ui`, `references/components.md`.

## Prüfen

Im Ordner `frontend/`: `npm run lint` (0 Fehler) und `npm run build` (Typprüfung + Bundle).
