export const COMMAND_CLASSES = [
  "flex size-full flex-col overflow-hidden rounded-xl! bg-popover p-1",
  "text-popover-foreground",
];

export const COMMAND_INPUT_GROUP_CLASSES = [
  "h-8! rounded-lg! border-input/30 bg-input/30 shadow-none!",
  "*:data-[slot=input-group-addon]:pl-2!",
];

export const COMMAND_LIST_CLASSES = [
  "no-scrollbar max-h-72 scroll-py-1 overflow-x-hidden overflow-y-auto outline-none",
];

export const COMMAND_GROUP_CLASSES = [
  "overflow-hidden p-1 text-foreground **:[[cmdk-group-heading]]:px-2",
  "**:[[cmdk-group-heading]]:py-1.5 **:[[cmdk-group-heading]]:text-xs",
  "**:[[cmdk-group-heading]]:font-medium **:[[cmdk-group-heading]]:text-muted-foreground",
];

export const COMMAND_ITEM_CLASSES = [
  "group/command-item relative flex cursor-default items-center gap-2 rounded-sm px-2",
  "py-1.5 text-sm outline-hidden select-none in-data-[slot=dialog-content]:rounded-lg!",
  "data-[disabled=true]:pointer-events-none data-[disabled=true]:opacity-50",
  "data-selected:bg-muted data-selected:text-foreground [&_svg]:pointer-events-none",
  "[&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  "data-selected:*:[svg]:text-foreground",
];

export const COMMAND_ITEM_KIT_CLASSES = [
  "data-selected:bg-primary-subtle data-selected:text-primary-subtle-foreground",
  "data-selected:inset-ring-2 data-selected:inset-ring-ring",
  "data-selected:*:[svg]:text-primary-subtle-foreground",
];

export const COMMAND_CHECK_CLASSES = [
  "ml-auto opacity-0 group-has-data-[slot=command-shortcut]/command-item:hidden",
  "group-data-[checked=true]/command-item:opacity-100",
];

export const COMMAND_SHORTCUT_CLASSES = [
  "ml-auto text-xs tracking-widest text-muted-foreground",
  "group-data-selected/command-item:text-foreground",
];
