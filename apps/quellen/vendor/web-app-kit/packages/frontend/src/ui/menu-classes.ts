export const MENU_HIGHLIGHT_CLASSES = [
  "data-highlighted:inset-ring-2 data-highlighted:inset-ring-ring",
];

export const MENU_SURFACE_CLASSES = [
  "rounded-lg bg-popover p-1 text-popover-foreground ring-1 ring-foreground/10 duration-100",
  "data-[side=bottom]:slide-in-from-top-2",
  "data-[side=left]:slide-in-from-right-2",
  "data-[side=right]:slide-in-from-left-2",
  "data-[side=top]:slide-in-from-bottom-2",
  "data-open:animate-in data-open:fade-in-0 data-open:zoom-in-95",
  "data-closed:animate-out data-closed:fade-out-0 data-closed:zoom-out-95",
];

export const MENU_ITEM_CLASSES = [
  ...MENU_HIGHLIGHT_CLASSES,
  "relative flex cursor-default items-center",
  "gap-1.5 rounded-md px-1.5 py-1 text-sm outline-hidden select-none",
  "focus:bg-primary-subtle focus:text-primary-subtle-foreground",
  "not-data-[variant=destructive]:focus:**:text-primary-subtle-foreground",
  "data-inset:pl-7 data-[variant=destructive]:text-destructive",
  "data-[variant=destructive]:focus:bg-destructive/10",
  "data-[variant=destructive]:focus:text-destructive",
  "dark:data-[variant=destructive]:focus:bg-destructive/20",
  "data-disabled:pointer-events-none data-disabled:opacity-50",
  "[&_svg]:pointer-events-none [&_svg]:shrink-0",
  "[&_svg:not([class*='size-'])]:size-4",
  "data-[variant=destructive]:*:[svg]:text-destructive",
];

export const MENU_SELECTABLE_ITEM_CLASSES = [
  ...MENU_HIGHLIGHT_CLASSES,
  "relative flex cursor-default items-center gap-1.5 rounded-md py-1 pr-8",
  "pl-1.5 text-sm outline-hidden select-none focus:bg-primary-subtle",
  "focus:text-primary-subtle-foreground",
  "data-inset:pl-7 data-disabled:pointer-events-none",
  "data-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
  "[&_svg:not([class*='size-'])]:size-4",
];

export const MENU_SUB_TRIGGER_CLASSES = [
  ...MENU_HIGHLIGHT_CLASSES,
  "flex cursor-default items-center gap-1.5 rounded-md px-1.5 py-1 text-sm",
  "outline-hidden select-none focus:bg-primary-subtle",
  "focus:text-primary-subtle-foreground",
  "not-data-[variant=destructive]:focus:**:text-primary-subtle-foreground",
  "data-inset:pl-7 data-open:bg-primary-subtle data-open:text-primary-subtle-foreground",
  "[&_svg]:pointer-events-none [&_svg]:shrink-0",
  "[&_svg:not([class*='size-'])]:size-4",
];

export const MENU_CHECK_INDICATOR_CLASSES = [
  "pointer-events-none absolute right-2 flex items-center justify-center",
  "text-primary",
];

export const MENU_LABEL_CLASSES =
  "px-1.5 py-1 text-xs font-medium text-muted-foreground data-inset:pl-7";

export const MENU_SEPARATOR_CLASSES = "-mx-1 my-1 h-px bg-border";

export const MENU_SHORTCUT_CLASSES = "ml-auto text-xs tracking-widest text-muted-foreground";
