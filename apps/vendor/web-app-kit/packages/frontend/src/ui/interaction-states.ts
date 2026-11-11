export const PRIMARY_SUBTLE_STATE_CLASSES = [
  "not-disabled:hover:bg-primary-subtle not-disabled:hover:text-primary-subtle-foreground",
  "not-disabled:active:border-primary not-disabled:active:bg-primary-subtle",
  "not-disabled:active:inset-ring-1 not-disabled:active:inset-ring-primary",
  "aria-expanded:border-primary aria-expanded:bg-primary-subtle",
  "aria-expanded:inset-ring-1 aria-expanded:inset-ring-primary",
  "aria-expanded:text-primary-subtle-foreground",
  "aria-expanded:**:text-primary-subtle-foreground",
  "aria-pressed:border-primary aria-pressed:bg-primary-subtle",
  "aria-pressed:inset-ring-1 aria-pressed:inset-ring-primary",
  "aria-pressed:text-primary-subtle-foreground",
  "aria-pressed:**:text-primary-subtle-foreground",
];

export const FILLED_FOCUS_CLASSES = [
  "focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solid",
  "focus-visible:outline-ring",
];

export const TOGGLE_ON_FOCUS_CLASSES = [
  "data-[state=on]:focus-visible:outline-2 data-[state=on]:focus-visible:outline-offset-2",
  "data-[state=on]:focus-visible:outline-solid data-[state=on]:focus-visible:outline-ring",
];
