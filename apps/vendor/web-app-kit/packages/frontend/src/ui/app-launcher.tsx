import { IconGridDots } from "@tabler/icons-react";
import { Button } from "./button.js";
import { cn } from "./cn.js";
import { INTERACTIVE_SURFACE_CLASSES } from "./interactive-surface.js";
import { Popover, PopoverContent, PopoverTrigger } from "./popover.js";
import type { AppLauncherItem, AppLauncherProps } from "./app-launcher.types.js";

const ITEM_CLASSES = "flex flex-col items-center gap-2 rounded-lg px-2 py-3 text-center";
const ICON_CLASSES = "text-primary [&_svg]:size-7";

function LauncherLink({ item, newTabHint }: { item: AppLauncherItem; newTabHint: string }) {
  return (
    <a
      href={item.href}
      target="_blank"
      rel="noopener"
      className={cn(INTERACTIVE_SURFACE_CLASSES, ITEM_CLASSES)}
    >
      <span aria-hidden className={ICON_CLASSES}>
        {item.icon}
      </span>
      <span className="line-clamp-2 w-full text-xs break-words">{item.label}</span>{" "}
      <span className="sr-only">{newTabHint}</span>
    </a>
  );
}

export function AppLauncher({ items, label, newTabHint }: AppLauncherProps) {
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon" aria-label={label}>
          <IconGridDots aria-hidden />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="end" className="w-96 max-w-[calc(100vw-2rem)]">
        <ul aria-label={label} className="grid grid-cols-3 gap-1">
          {items.map((item) => (
            <li key={item.id}>
              <LauncherLink item={item} newTabHint={newTabHint} />
            </li>
          ))}
        </ul>
      </PopoverContent>
    </Popover>
  );
}
