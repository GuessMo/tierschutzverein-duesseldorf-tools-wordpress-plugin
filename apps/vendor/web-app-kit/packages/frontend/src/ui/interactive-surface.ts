const SELECTED_SURFACE_CLASSES = [
  "aria-selected:bg-primary-subtle aria-selected:inset-ring-2 aria-selected:inset-ring-primary",
  "aria-[current=page]:bg-primary-subtle aria-[current=page]:inset-ring-2",
  "aria-[current=page]:inset-ring-primary aria-[current=true]:bg-primary-subtle",
  "aria-[current=true]:inset-ring-2 aria-[current=true]:inset-ring-primary",
  "data-selected:bg-primary-subtle data-selected:inset-ring-2 data-selected:inset-ring-primary",
];

export const INTERACTIVE_SURFACE_CLASSES = [
  "cursor-pointer text-left outline-none focus-visible:outline-hidden",
  "transition-[box-shadow,translate]",
  "not-disabled:hover:bg-primary-subtle not-disabled:active:translate-y-px",
  "focus-visible:ring-2 focus-visible:ring-ring",
  "disabled:cursor-not-allowed disabled:opacity-50",
  "aria-disabled:pointer-events-none aria-disabled:opacity-50",
  ...SELECTED_SURFACE_CLASSES,
].join(" ");

export const STRETCHED_CARD_CLASSES = [
  "relative [&:hover:not(:has([data-slot=card-action]:hover))]:bg-primary-subtle",
  "[&:active:not(:has([data-slot=card-action]:active))]:translate-y-px",
  "transition-[translate]",
  ...SELECTED_SURFACE_CLASSES,
].join(" ");

export const STRETCHED_LINK_CLASSES = [
  "outline-none focus-visible:outline-hidden",
  "after:absolute after:inset-0 after:rounded-xl",
  "focus-visible:after:ring-2 focus-visible:after:ring-ring focus-visible:after:ring-inset",
].join(" ");
