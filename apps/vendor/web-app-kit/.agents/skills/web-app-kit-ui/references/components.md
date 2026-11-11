# Kit-Bausteine: Katalog und Pflichtregeln

Import immer aus `@web-app-kit/frontend/ui` (UI) bzw. `@web-app-kit/frontend` (Theme,
API-Client). Was hier nicht steht, gibt es im Kit nicht: nicht lokal nachbauen, sondern im
Kit-Repository ergänzen lassen (siehe SKILL.md, „Fehlender Baustein"). Den tatsächlichen
Bestand der gepinnten Version zeigt
`cat vendor/web-app-kit/packages/frontend/src/ui/index.ts`.

## Rahmen und Layout

| Baustein | Pflicht-Props | Regeln |
| --- | --- | --- |
| `AppShell` | `brand`, `skipLinkLabel`, `children` | `navigation` verlangt `navigationLabel`, `detail` verlangt `detailLabel`; `homeHref` macht `brand` zum Link; `renderLink` für Router-Links (alle Props außer `href` unverändert weitergeben); `actions` links nach dem Titel, `controls` rechts (Sprache, Theme); `footer` für `AppVersion` |
| `SidePanel` | `side` (`left`/`right`) | `width` `sm` 16rem (Default), `md` 20rem, `lg` 24rem; `title` macht das Panel zur benannten Gruppe mit `<h2>`; `onOpenChange` verlangt `closeLabel`; `returnFocusRef` auf das auslösende Element; `open={false}` rendert nichts |
| `PageContainer` | – | `size` `md` (Formulare, Lesetext), `lg` (Listen, Default), `full` (Arbeitsflächen) |
| `AppVersion` | `label`, `version` | nur im `footer` der `AppShell`; `kitLabel` (Pflicht in Kit-Tools) zeigt dahinter die Kit-Version, z. B. „Version v1.2.0 · Kit 0.14.0“ |
| `ThemeToggle` | `labels` (`system`, `light`, `dark`), `describe` | Zyklus System → Hell → Dunkel; `describe` formuliert die Aktion für `aria-label`/`title` |
| `LanguageToggle` | `languages`, `value`, `onChange`, `describe` | `describe` muss den sichtbaren Sprachcode enthalten (WCAG 2.5.3) |

Aufbau einer Seite mit Liste und Detail:

```tsx
<AppShell
  brand={t("app.title")}
  homeHref="/"
  skipLinkLabel={t("layout.skipToContent")}
  controls={<><LocalizedLanguageToggle /><LocalizedThemeToggle /></>}
  detail={
    <SidePanel
      side="right"
      width="lg"
      title={selected?.name}
      open={selected !== null}
      onOpenChange={() => setSelected(null)}
      closeLabel={t("common.close")}
      returnFocusRef={selectedRowRef}
    >
      <ItemDetail item={selected} />
    </SidePanel>
  }
  detailLabel={t("items.detail")}
  footer={
    <AppVersion
      label={t("layout.version")}
      version={version}
      kitLabel={t("layout.kitVersion")}
    />
  }
>
  <PageContainer size="lg">…</PageContainer>
</AppShell>
```

Unter 48rem stapeln sich Navigation, Inhalt und Detail; jedes Panel ist auf `30dvh`
begrenzt. Detail-Panel deshalb erst nach einer Auswahl öffnen. Nur `<main>` scrollt.

## Aktionen und Anzeige

| Baustein | Varianten | Regeln |
| --- | --- | --- |
| `Button` | `variant`: `default`, `outline`, `secondary`, `tertiary`, `ghost`, `destructive`, `link`; `size`: `default`, `xs`, `sm`, `lg`, `icon`, `icon-xs`, `icon-sm`, `icon-lg` | eine `default`-Aktion pro Ansicht, `secondary` für die zweite, `tertiary` für neutrale oder untergeordnete Aktionen; `xs`/`icon-xs` sind 24px hoch (WCAG-Minimum), sonst Default-Größen; Icon-only-Button braucht `aria-label` aus i18n; Laden: `disabled` + sichtbarer Text, kein eigenes `isPending` |
| `Badge` | wie Button-Farbvarianten, dazu `info` und `warning` (getönte Fläche, Text in `--info` bzw. `--warning`) | als Link: `Badge asChild` mit `<a>`; Status nie nur über Farbe, der Text trägt die Bedeutung; keine eigenen Klassen wie `bg-[var(--info)]` |
| `MetaRow` | `label` optional | umbrechende Reihe kleiner Meta-Angaben (Badges für Status, Version, Kategorie); mit `label` fett vorangestellt und als `role="group"` benannt; Text kommt per Props/Kinder aus i18n; ersetzt lokale Meta-Zeilen (`flex flex-wrap gap-2`) |
| `Card`, `CardHeader`, `CardTitle`, `CardDescription`, `CardAction`, `CardContent`, `CardFooter` | `interactive` (nur mit `asChild`) | keine verschachtelten Karten |
| `Table`, `TableHeader`, `TableBody`, `TableRow`, `TableHead`, `TableCell`, `TableCaption`, `TableFooter` | – | ausgewählte Zeile: `data-state="selected"`; `TableCaption` oder `aria-label` benennt die Tabelle |
| `Tooltip`, `TooltipTrigger`, `TooltipContent`, `TooltipProvider` | – | nur Zusatzinfo; nie die einzige Beschriftung eines Bedienelements |
| `Progress` | `value`, `max` (Default 100); `value={null}` = unbestimmt | Pflicht `aria-label` oder `aria-labelledby` (Typ erzwingt eins von beiden); Indikator `--primary` auf Spur `--muted`; Bewegung entfällt bei reduzierter Bewegung |
| `Skeleton` | Form per `className` (`h-*`, `w-*`, `size-*`, `rounded-*`) | dekorativ (`aria-hidden`); Ladezustand am Container per `aria-busy` oder sichtbarem Text melden; Fläche `--accent`, pulsiert nicht bei reduzierter Bewegung |
| `Toaster`, `toast` | `toast()`, `toast.success`, `.info`, `.warning`, `.error`, `.loading`; `action`, `description` | `Toaster` einmal pro App innerhalb von `ThemeProvider`, Pflicht `closeLabel` und `regionLabel` aus i18n; Peer-Dependency `sonner` installieren; `toast` nur aus `@web-app-kit/frontend/ui` importieren; nur Rückmeldung nach einer Aktion, Fehler, die eine Entscheidung brauchen, im Dialog |

## Interaktive Karten und eigene Klick-Flächen

| Fall | Muster |
| --- | --- |
| ganze Karte ist ein Link oder eine Aktion | `<Card interactive asChild><a href=…>` bzw. `<button type="button">`; im `<button>` `CardHeader`/`CardTitle`/`CardDescription` per `asChild` als `<span>` |
| Karte mit eigenen Buttons | Karte bleibt `div` mit `STRETCHED_CARD_CLASSES`, Titel-Link mit `STRETCHED_LINK_CLASSES`, Aktionen in `CardAction className="relative z-10"` |
| eigene Listenzeile, Kachel | `className={cn(INTERACTIVE_SURFACE_CLASSES, "w-full rounded-md px-3 py-2")}` auf `<button>`/`<a>` |

Keine Buttons oder Links innerhalb einer Karte nach Muster 1 (verschachtelte interaktive
Elemente sind unzulässig). Klassen bei `asChild` an `Card` geben, nicht an das Kind.

## Listen

Einfache Listen (eine Spalte, Auswahl öffnet das Detail) als `<ul>` aus Kit-Klassen und
Tokens; mehrspaltige Daten als `Table*`. Zustände Laden, Fehler, leer und gefüllt sind Pflicht,
alle Texte per i18n.

`ItemList.types.ts`:

```ts
export interface Item {
  id: string;
  name: string;
}

export interface ItemListProps {
  items: Item[];
  isLoading: boolean;
  loadError: unknown;
  selectedId: string | null;
  onSelect: (id: string) => void;
  onCreate?: () => void;
}
```

`ItemList.tsx`:

```tsx
import { Button, cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { localizeError } from "@/api/errorMessage";
import { useLanguage } from "@/i18n";
import type { ItemListProps } from "./ItemList.types";

const ROW_CLASSES = cn(
  INTERACTIVE_SURFACE_CLASSES,
  "flex min-h-10 w-full items-center gap-3 rounded-md border border-border bg-card px-3 py-2",
);

export function ItemList({
  items,
  isLoading,
  loadError,
  selectedId,
  onSelect,
  onCreate,
}: ItemListProps) {
  const { t } = useLanguage();
  const isEmpty = !isLoading && loadError === null && items.length === 0;
  return (
    <section aria-labelledby="items-heading" className="flex flex-col gap-4">
      <h2 id="items-heading" className="text-lg font-semibold">
        {t("items.heading")}
      </h2>
      <p role="status" className="text-sm text-muted-foreground">
        {isLoading ? t("items.loading") : null}
      </p>
      {loadError !== null && (
        <p role="alert" className="text-sm text-destructive">
          {localizeError(loadError, t)}
        </p>
      )}
      {isEmpty && (
        <div className="flex flex-col items-start gap-4 rounded-md border border-dashed p-6">
          <p className="text-muted-foreground">{t("items.empty")}</p>
          {onCreate && <Button onClick={onCreate}>{t("items.new")}</Button>}
        </div>
      )}
      {items.length > 0 && (
        <ul aria-labelledby="items-heading" aria-busy={isLoading} className="flex flex-col gap-2">
          {items.map((item) => (
            <li key={item.id}>
              <button
                type="button"
                aria-current={item.id === selectedId ? "true" : undefined}
                onClick={() => onSelect(item.id)}
                className={ROW_CLASSES}
              >
                <span className="min-w-0 flex-1 truncate">{item.name}</span>
              </button>
            </li>
          ))}
        </ul>
      )}
    </section>
  );
}
```

| Punkt | Regel |
| --- | --- |
| Laden | `role="status"` ist immer gerendert, nur der Text wechselt, sonst sagen Screenreader nichts an; `aria-busy` nur am Bereich, der sich ändert (`<ul>` beim Neuladen), die Live-Region liegt außerhalb davon |
| Fehler | `role="alert"` mit `localizeError(loadError, t)`, nie nur Farbe |
| Leer | Text, was fehlt und wie es entsteht; Anlegen-Button nur, wenn die Ansicht sonst keine primäre Aktion zeigt |
| Zeile | ganze Zeile ist ein `<button>` (oder `<a>` bei eigener URL) mit `INTERACTIVE_SURFACE_CLASSES`: hover, gedrückt, focus-visible, deaktiviert; Höhe mindestens 24px (hier `min-h-10` = 40px), Zeilenabstand `gap-2` |
| Auswahl | `aria-current="true"`; `INTERACTIVE_SURFACE_CLASSES` zeigt dann `bg-primary-subtle` plus Primär-Innenring, nicht nur Farbe allein ohne Attribut |
| Lange Namen | `min-w-0 flex-1 truncate`, voller Name im Detail |
| Nur Anzeige | ohne Interaktion `<li className="rounded-md border px-3 py-2">`, kein `<button>` |

Schlüssel in `de.ts`/`en.ts`: `items.heading`, `items.loading`, `items.empty`, `items.new`.

## Dialoge

```tsx
<Dialog open={open} onOpenChange={setOpen}>
  <DialogTrigger asChild>
    <Button>{t("items.new")}</Button>
  </DialogTrigger>
  <DialogContent closeLabel={t("common.close")}>
    <DialogHeader>
      <DialogTitle>{t("items.newTitle")}</DialogTitle>
      <DialogDescription>{t("items.newDescription")}</DialogDescription>
    </DialogHeader>
    <ItemForm onDone={() => setOpen(false)} />
    <DialogFooter>
      <DialogClose asChild>
        <Button variant="outline">{t("common.cancel")}</Button>
      </DialogClose>
      <Button type="submit" form="item-form">{t("common.save")}</Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
```

- `DialogTitle` immer, auch wenn optisch versteckt (`className="sr-only"`).
- `DialogContent` verlangt `closeLabel`, solange der Schließen-Button sichtbar ist
  (`showCloseButton={false}` hebt das auf). `DialogFooter showCloseButton` verlangt ebenfalls
  `closeLabel`.
- Escape schließt, Fokus geht beim Öffnen in den Dialog und beim Schließen zurück zum Auslöser
  (Radix-Standard, nicht abschalten). Keine eigene Tastaturbehandlung, die Escape abfängt.
- Destruktive Bestätigung: `Button variant="destructive"` mit konkretem Text („Projekt
  löschen"), nicht „OK".

### Bestätigung vor folgenreicher Aktion: `AlertDialog`

```tsx
<AlertDialog>
  <AlertDialogTrigger asChild>
    <Button variant="destructive">{t("project.delete")}</Button>
  </AlertDialogTrigger>
  <AlertDialogContent size="sm">
    <AlertDialogHeader>
      <AlertDialogMedia>
        <IconTrash aria-hidden />
      </AlertDialogMedia>
      <AlertDialogTitle>{t("project.deleteTitle")}</AlertDialogTitle>
      <AlertDialogDescription>{t("project.deleteDescription")}</AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel>{t("common.keep")}</AlertDialogCancel>
      <AlertDialogAction variant="destructive" onClick={remove}>
        {t("common.delete")}
      </AlertDialogAction>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
```

- `AlertDialogCancel` und `AlertDialogAction` verlangen ihren Text als `children` (kein
  eingebautes „Cancel"/„Continue"). `AlertDialogTitle` und `AlertDialogDescription` immer.
- Kein Schließen per Klick daneben; Escape bricht ab, der Fokus startet auf
  `AlertDialogCancel` und kehrt zum Auslöser zurück (Radix, nicht überschreiben).
- `size`: `default` oder `sm` (Buttons nebeneinander); Action-`variant` wie `Button`.
- Für Formulare oder Inhalte mit eigener Bedienung `Dialog`, nicht `AlertDialog`.

## Menüs und Auswahl

| Baustein | Regeln |
| --- | --- |
| `DropdownMenu*` (`Trigger`, `Content`, `Item`, `CheckboxItem`, `RadioGroup`, `RadioItem`, `Label`, `Separator`, `Sub*`, `Shortcut`) | Trigger per `asChild` auf `Button`; ein eigenes `<button>` als Trigger braucht eigene Zustände |
| `Select`, `SelectTrigger`, `SelectValue`, `SelectContent`, `SelectGroup`, `SelectItem`, `SelectLabel`, `SelectSeparator` | Items immer in `SelectGroup`; Standard `position="item-aligned"`, Popover-Verhalten per `position="popper"`; sichtbares `Label` mit `htmlFor` auf die `id` des Triggers |
| `Tabs`, `TabsList`, `TabsTrigger`, `TabsContent` | gleichrangige Ansichten auf einer Seite; `TabsList` per `aria-label` oder `aria-labelledby` benennen; `variant` `default` (aktiver Tab mit Primärrahmen und Innenring) oder `line` (Unterstrich); `orientation` `horizontal`/`vertical`; ein Tab-Stopp, Pfeiltasten wechseln, deaktivierte Tabs werden übersprungen; nicht als Navigation zwischen Seiten |
| `ContextMenu*` (`Trigger`, `Content`, `Item`, `CheckboxItem`, `RadioGroup`, `RadioItem`, `Label`, `Separator`, `Shortcut`, `Group`, `Portal`, `Sub`, `SubTrigger`, `SubContent`) | Trigger fokussierbar machen (`tabIndex={0}` oder `asChild` auf ein fokussierbares Element), dann öffnen Rechtsklick, Shift+F10 und die Kontextmenü-Taste; Escape schließt, Fokus kehrt zum Trigger zurück; jede Aktion zusätzlich ohne Rechtsklick anbieten (Werkzeugleiste oder `DropdownMenu`) |
| `Command`, `CommandInput`, `CommandList`, `CommandEmpty`, `CommandGroup`, `CommandItem`, `CommandSeparator`, `CommandShortcut`, `CommandDialog` | Import aus `@web-app-kit/frontend/command`, nicht aus `/ui`; durchsuchbare Liste auf `cmdk` (Peer-Dependency installieren); `Command` per `label` benennen (wird Name des Suchfelds); `CommandDialog` verlangt `title` und `description`, `closeLabel` zeigt den Schließen-Knopf; markierter Eintrag wie Menüs |
| `AppLauncher` (optional) | Tool-Auswahl oben rechts in `AppShell controls`: Punkte-Raster öffnet ein Popup mit drei Spalten; Pflicht `items` (`id`, `label`, `href`, Tabler-`icon`), `label` (Name von Trigger und Liste), `newTabHint` (sr-only, jedes Tool öffnet in neuem Tab); Einträge mit `INTERACTIVE_SURFACE_CLASSES` |
| `AccountMenu` (optional) | Ganz rechts in `AppShell controls`, nach `AppLauncher`; gefüllter runder Button mit Initialen, Menü mit Name und Abmelden; Pflicht `name`, `initials`, `label`, `logoutLabel`, `onLogout`; farbige Initialen nie als `span` in einen Ghost-Button legen (offenes Menü überschreibt die Kindfarbe) |
| `MultiCombobox` | Import aus `@web-app-kit/frontend/command`; Mehrfachauswahl aus vielen Optionen (ab etwa 10, sonst Checkboxen); Pflicht `options`, `value`, `onValueChange`, `placeholder`, `searchPlaceholder`, `emptyText`, `selectedLabel`, `removeLabel`; sichtbares `Label` mit `htmlFor` auf `id`; Werte ohne Option erscheinen als Chip mit Rohwert; Peer-Dependency `cmdk` |

Markierte Einträge in `DropdownMenu`, `ContextMenu` und `Select` zeigen die zarte Fläche plus
einen inneren 2-px-Ring in `--ring` (≥ 3:1). Nicht per `className` überschreiben.

## Werkzeugleisten und Popups

| Baustein | Varianten | Regeln |
| --- | --- | --- |
| `Toggle` | `variant`: `default`, `outline`; `size` wie `Button` (`default`, `xs`, `sm`, `lg`, `icon`, `icon-xs`, `icon-sm`, `icon-lg`) | einzelner An/Aus-Schalter (`pressed`, `onPressedChange`); aus: Hover `--primary-subtle`, gedrückt Primärrahmen plus Innenring; aktiv = Fläche `--primary` (Hover `--primary-hover`, gedrückt `--primary-active`), Schrift `--primary-foreground`; Icon-only braucht `aria-label` aus i18n |
| `ToggleGroup`, `ToggleGroupItem` | `type`: `single` (ein aktives Werkzeug), `multiple` (unabhängige Schalter); `variant`, `size`, `spacing`, `orientation` an der Gruppe | Pflicht `label` (wird `aria-label`) oder `aria-labelledby`; ein Tab-Stopp, Pfeiltasten wechseln das Item; bei `single` leeren Wert abfangen (`next && setTool(next)`) |
| `Popover`, `PopoverTrigger`, `PopoverContent`, `PopoverAnchor`, `PopoverHeader`, `PopoverTitle`, `PopoverDescription` | `align`, `side`, `sideOffset` an `PopoverContent` | Trigger per `asChild` auf `Button`/`Toggle`; Fokus, Escape und Rückkehr zum Trigger aus Radix, nicht überschreiben; für Menüs mit Aktionen `DropdownMenu`, für modale Aufgaben `Dialog` |

Nicht `Button aria-pressed` für eine Werkzeugauswahl nutzen: `ToggleGroup type="single"` bringt
Radiogruppen-Semantik und Pfeiltasten mit, `Button aria-pressed` zeigt den Aktiv-Zustand nur
mit 14 % Fläche statt Primärfarbe.

### Muster: Werkzeugleiste mit aktivem Werkzeug

`EditorToolbar.types.ts`:

```ts
export type EditorTool = "select" | "frame" | "text";

export interface EditorToolbarProps {
  tool: EditorTool;
  onToolChange: (tool: EditorTool) => void;
  isGridVisible: boolean;
  onGridVisibleChange: (isVisible: boolean) => void;
}
```

`EditorToolbar.tsx`:

```tsx
import { IconGridDots, IconPointer, IconSquare, IconTypography } from "@tabler/icons-react";
import { Toggle, ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import { useLanguage } from "@/i18n";
import type { EditorTool, EditorToolbarProps } from "./EditorToolbar.types";

const TOOLS = [
  { value: "select", icon: IconPointer, labelKey: "editor.toolSelect" },
  { value: "frame", icon: IconSquare, labelKey: "editor.toolFrame" },
  { value: "text", icon: IconTypography, labelKey: "editor.toolText" },
] as const;

export function EditorToolbar({
  tool,
  onToolChange,
  isGridVisible,
  onGridVisibleChange,
}: EditorToolbarProps) {
  const { t } = useLanguage();
  return (
    <div className="flex items-center gap-2 border-b px-2 py-1">
      <ToggleGroup
        type="single"
        label={t("editor.tools")}
        value={tool}
        onValueChange={(next) => next && onToolChange(next as EditorTool)}
        variant="outline"
        size="icon"
      >
        {TOOLS.map(({ value, icon: Icon, labelKey }) => (
          <ToggleGroupItem key={value} value={value} aria-label={t(labelKey)}>
            <Icon aria-hidden />
          </ToggleGroupItem>
        ))}
      </ToggleGroup>
      <Toggle
        variant="outline"
        size="icon"
        pressed={isGridVisible}
        onPressedChange={onGridVisibleChange}
        aria-label={t("editor.grid")}
      >
        <IconGridDots aria-hidden />
      </Toggle>
    </div>
  );
}
```

| Punkt | Regel |
| --- | --- |
| Aktiv erkennbar | Kit-Stil nicht überschreiben: aktive Items tragen `--primary`, keine eigene Aktiv-Farbe |
| Auswahl nie leer | `single`: Radix liefert beim Klick auf das aktive Item `""`; `next && …` hält das Werkzeug |
| Beschriftung | Gruppe per `label`, jedes Icon-Item per `aria-label`, beides aus i18n; `Tooltip` nur zusätzlich |
| Tastatur | Tab erreicht die Gruppe einmal, Pfeiltasten wählen das Item, keine eigene Tastaturbehandlung |
| Einstellungen | Optionen eines Werkzeugs in `Popover` neben der Gruppe, Trigger `Button variant="ghost" size="icon"` |

Schlüssel in `de.ts`/`en.ts`: `editor.tools`, `editor.toolSelect`, `editor.toolFrame`,
`editor.toolText`, `editor.grid`.

## Formulare

| Baustein | Regeln |
| --- | --- |
| `Label` + `Input` / `Textarea` / `Select` | jedes Feld hat ein sichtbares `Label` mit `htmlFor` = `id` des Feldes; Placeholder ist kein Label |
| `Checkbox` | `checked`/`defaultChecked` (`true`, `false`, `"indeterminate"`), `onCheckedChange` liefert `true`, `false` oder `"indeterminate"` (`state === true` prüfen); Beschriftung per `Label htmlFor` oder `aria-label` aus i18n; an/teilweise in `--primary`, Fokus als Outline mit Abstand |
| `Switch` | Einstellung, die sofort wirkt (`checked`/`defaultChecked`, `onCheckedChange`); für Auswahl mit Absenden `Checkbox`; `size` `default`/`sm`; Beschriftung per `Label htmlFor` oder `aria-label` aus i18n; an in `--primary`, aus mit Spur `--input` (≥ 3:1 zu Seite und Card), Fokus als Outline mit Abstand |
| `InputGroup`, `InputGroupAddon`, `InputGroupInput`, `InputGroupTextarea`, `InputGroupButton`, `InputGroupText` | Feld mit Zusätzen (Icon, Text, Knopf); `align` am Addon: `inline-start` (Default), `inline-end`, `block-start`, `block-end`; `aria-invalid` und `disabled` an das Feld, die Gruppe übernimmt Rahmen und Abblendung; Icon-Knopf braucht `aria-label` aus i18n |
| `Separator` | `orientation` `horizontal`/`vertical`; dekorativ per Default (`role="none"`), `decorative={false}` nur, wenn die Trennung Bedeutung trägt |

State: `nameErrorKey: TranslationKey | null` (eigene Prüfung), `submitError: unknown`
(Antwort des Backends), beide ohne fertigen Text. Ablauf von `handleSubmit`: Skill
`web-app-kit-new-tool`, `references/frontend.md`, „Fehlermeldungen i18n-sicher".

```tsx
<form id="item-form" onSubmit={handleSubmit} className="flex flex-col gap-4" noValidate>
  <div className="flex flex-col gap-2">
    <Label htmlFor="item-name">{t("items.name")}</Label>
    <Input
      id="item-name"
      value={name}
      onChange={(event) => setName(event.target.value)}
      required
      aria-invalid={nameErrorKey ? true : undefined}
      aria-describedby={nameErrorKey ? "item-name-error" : undefined}
    />
    {nameErrorKey && (
      <p id="item-name-error" className="text-sm text-destructive">
        {t(nameErrorKey)}
      </p>
    )}
  </div>
  {submitError !== null && (
    <p role="alert" className="text-sm text-destructive">
      {localizeError(submitError, t, { 409: "items.nameTaken" })}
    </p>
  )}
</form>
```

- Fehler als Text am Feld (`aria-describedby`), nicht nur roter Rahmen.
- Übersetzt wird erst beim Rendern (`t(nameErrorKey)`, `localizeError(submitError, t)`), damit
  ein Sprachwechsel die Meldung mitzieht. Kein `new UserFacingError(t("…"))`.
- Formularweite Fehler (Antwort des Backends) als `<p role="alert">`.
- Abstände mit `flex flex-col gap-*`, nicht `space-y-*`.

## Zustände (hover, gedrückt, Fokus, deaktiviert)

Kit-Komponenten bringen alle Zustände mit. Für eigene interaktive Elemente:

| Zustand | Klassen |
| --- | --- |
| Hover | `hover:bg-primary-subtle hover:text-primary-subtle-foreground` bzw. `INTERACTIVE_SURFACE_CLASSES` |
| gedrückt | `active:translate-y-px`, bei Buttons ohne Fläche zusätzlich `active:border-primary active:inset-ring-1 active:inset-ring-primary` |
| ausgewählt / geöffnet | `aria-pressed`/`aria-expanded`/`aria-current` + `border-primary inset-ring-1 inset-ring-primary bg-primary-subtle` |
| Primärfläche | `bg-primary hover:bg-primary-hover active:bg-primary-active` |
| Link | `text-primary hover:text-primary-hover hover:underline` |
| Fokus | `focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-hidden` (voll deckend; ein Halo `ring-ring/50` nur zusammen mit `focus-visible:border-ring` an einem Element mit Rahmen) |
| deaktiviert | `disabled:cursor-not-allowed disabled:opacity-50` |

Verboten: `hover:bg-muted`, `hover:bg-accent` (in Hell auf weißen Flächen kaum sichtbar),
eigene `color-mix`-Hover, Fokus per `:focus` statt `:focus-visible`, entfernter Fokusring.

## ESLint-Regeln

### `web-app-kit/interactive-states`

Prüft `button`, `a href`, alles mit `onClick`/`onKeyDown` und interaktive `role`s. Erfüllt,
wenn `className` je eine `hover:`-, `active:`- und `focus-visible:`-Klasse enthält (unbedingt,
nicht nur hinter `&&`) oder einen Kit-Export wie `INTERACTIVE_SURFACE_CLASSES`,
`buttonVariants(…)`, `badgeVariants(…)` nutzt. Kinder von `Button asChild`, `Badge asChild`,
`Card interactive asChild` gelten als gedeckt; Radix-Trigger (`DropdownMenuTrigger asChild`)
nicht. Meldet zusätzlich `hover:bg-muted`/`hover:bg-accent`. Dazu laufen
`jsx-a11y/no-static-element-interactions`, `click-events-have-key-events`,
`interactive-supports-focus`, `no-noninteractive-element-interactions`: Klicks gehören auf
`<button>`/`<a>`, nicht auf `div`.

### `web-app-kit/no-foreign-ui`

Meldet Importe aus `radix-ui`, `@radix-ui/*`, `shadcn` (außer `shadcn/tailwind.css`), aus
Icon-Paketen außer `@tabler/icons-react` (z. B. `lucide-react`, `react-icons`, `@heroicons/*`)
und aus lokalen Kopien (`@/components/ui/*`, `../components/ui/*`); dazu jede Datei unter
`components/ui/`. Eigene Elemente aus Kit-Bausteinen in anderen Ordnern
(`src/components/app/`) sind erlaubt. Begründete Ausnahmen über die Option `allow`
(Import-Quelle oder Dateipfad relativ zum ESLint-Arbeitsordner):

```js
export default [
  ...webAppKit.configs.recommended,
  { rules: { "web-app-kit/no-foreign-ui": ["error", { allow: ["recharts"] }] } },
];
```

Bei einer Meldung den Code korrigieren, nicht die Regel abschalten.
