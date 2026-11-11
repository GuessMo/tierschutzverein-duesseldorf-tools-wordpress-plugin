import { IconX } from "@tabler/icons-react";
import { useId, type RefObject } from "react";
import { APP_SHELL_CONTENT_ID } from "./app-shell.constants.js";
import { Button } from "./button.js";
import { cn } from "./cn.js";
import type {
  SidePanelCloseButtonProps,
  SidePanelFrameProps,
  SidePanelHeaderProps,
  SidePanelProps,
  SidePanelSide,
  SidePanelWidth,
} from "./side-panel.types.js";

const WIDTH_CLASSES: Record<SidePanelWidth, string> = {
  sm: "md:w-64",
  md: "md:w-80",
  lg: "md:w-96",
};

const BORDER_CLASSES: Record<SidePanelSide, string> = {
  left: "border-b md:border-b-0 md:border-r",
  right: "border-t md:border-t-0 md:border-l",
};

const PANEL_CLASSES = [
  "flex w-full shrink-0 flex-col md:h-full",
  "border-sidebar-border bg-sidebar text-sidebar-foreground",
].join(" ");

function SidePanelCloseButton({ onClose, closeLabel }: SidePanelCloseButtonProps) {
  return (
    <Button
      type="button"
      variant="ghost"
      size="icon-sm"
      aria-label={closeLabel}
      title={closeLabel}
      onClick={onClose}
    >
      <IconX />
    </Button>
  );
}

function SidePanelHeader({
  title,
  headingId,
  actions,
  onClose,
  closeLabel,
}: SidePanelHeaderProps) {
  if (!title && !actions && !onClose) return null;
  return (
    <div
      data-slot="side-panel-header"
      className="flex h-12 shrink-0 items-center gap-2 border-b border-sidebar-border px-4"
    >
      {title && (
        <h2 id={headingId} className="min-w-0 flex-1 truncate text-sm font-semibold">
          {title}
        </h2>
      )}
      <div className="ml-auto flex shrink-0 items-center gap-1">
        {actions}
        {onClose && <SidePanelCloseButton onClose={onClose} closeLabel={closeLabel} />}
      </div>
    </div>
  );
}

function createCloseHandler(
  onOpenChange?: (open: boolean) => void,
  returnFocusRef?: RefObject<HTMLElement | null>,
) {
  if (!onOpenChange) return undefined;
  return () => {
    onOpenChange(false);
    const target = returnFocusRef?.current ?? document.getElementById(APP_SHELL_CONTENT_ID);
    target?.focus();
  };
}

function SidePanelFrame({ side, width = "sm", className, children, ...rest }: SidePanelFrameProps) {
  const { title, actions, onOpenChange, closeLabel, returnFocusRef, ...props } = rest;
  const headingId = useId();
  return (
    <div
      data-slot="side-panel"
      data-side={side}
      role={title ? "group" : undefined}
      aria-labelledby={title ? headingId : undefined}
      className={cn(PANEL_CLASSES, WIDTH_CLASSES[width], BORDER_CLASSES[side], className)}
      {...props}
    >
      <SidePanelHeader
        title={title}
        headingId={headingId}
        actions={actions}
        onClose={createCloseHandler(onOpenChange, returnFocusRef)}
        closeLabel={closeLabel}
      />
      <div data-slot="side-panel-body" className="min-h-0 flex-1 overflow-y-auto p-2">
        {children}
      </div>
    </div>
  );
}

export function SidePanel({ open = true, ...props }: SidePanelProps) {
  if (!open) return null;
  return <SidePanelFrame {...props} />;
}
