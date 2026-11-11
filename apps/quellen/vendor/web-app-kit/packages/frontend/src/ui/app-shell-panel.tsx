import { IconChevronDown } from "@tabler/icons-react";
import { useId, useState } from "react";
import { cn } from "./cn.js";
import { INTERACTIVE_SURFACE_CLASSES } from "./interactive-surface.js";
import type { AppShellPanelPosition, AppShellPanelProps } from "./app-shell.types.js";

const PANEL_CLASSES = "flex min-h-0 shrink-0 flex-col max-md:max-h-[60dvh]";

const TOGGLE_CLASSES = [
  "flex min-h-11 w-full shrink-0 items-center justify-between gap-2 px-4 md:hidden",
  "border-sidebar-border bg-sidebar text-sm font-semibold text-sidebar-foreground",
  "focus-visible:ring-inset",
].join(" ");

const TOGGLE_BORDER_CLASSES: Record<AppShellPanelPosition, string> = {
  start: "border-b",
  end: "border-t",
};

const CHEVRON_CLASSES = "size-4 shrink-0 transition-transform motion-reduce:transition-none";

export function AppShellPanel({ as: Element, label, position, children }: AppShellPanelProps) {
  const [isExpanded, setIsExpanded] = useState(false);
  const contentId = useId();

  return (
    <Element
      aria-label={label}
      data-slot="app-shell-panel"
      data-expanded={isExpanded}
      className={PANEL_CLASSES}
    >
      <button
        type="button"
        aria-expanded={isExpanded}
        aria-controls={contentId}
        className={cn(INTERACTIVE_SURFACE_CLASSES, TOGGLE_CLASSES, TOGGLE_BORDER_CLASSES[position])}
        onClick={() => setIsExpanded((current) => !current)}
      >
        {label}
        <IconChevronDown aria-hidden className={cn(CHEVRON_CLASSES, isExpanded && "rotate-180")} />
      </button>
      <div id={contentId} className={cn("flex min-h-0 flex-1", !isExpanded && "max-md:hidden")}>
        {children}
      </div>
    </Element>
  );
}
