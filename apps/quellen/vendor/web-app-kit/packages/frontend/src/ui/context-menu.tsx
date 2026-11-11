import * as React from "react";
import { ContextMenu as ContextMenuPrimitive } from "radix-ui";
import { IconChevronRight } from "@tabler/icons-react";
import { cn } from "./cn.js";
import {
  MENU_LABEL_CLASSES,
  MENU_SEPARATOR_CLASSES,
  MENU_SHORTCUT_CLASSES,
  MENU_SUB_TRIGGER_CLASSES,
  MENU_SURFACE_CLASSES,
} from "./menu-classes.js";
import { openContextMenuFromKeyboard } from "./context-menu-keyboard.js";

function ContextMenu({ ...props }: React.ComponentProps<typeof ContextMenuPrimitive.Root>) {
  return <ContextMenuPrimitive.Root data-slot="context-menu" {...props} />;
}

function ContextMenuTrigger({
  className,
  onKeyDown,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.Trigger>) {
  return (
    <ContextMenuPrimitive.Trigger
      data-slot="context-menu-trigger"
      className={cn("select-none", className)}
      onKeyDown={(event) => {
        onKeyDown?.(event);
        if (!event.defaultPrevented) openContextMenuFromKeyboard(event);
      }}
      {...props}
    />
  );
}

function ContextMenuGroup({ ...props }: React.ComponentProps<typeof ContextMenuPrimitive.Group>) {
  return <ContextMenuPrimitive.Group data-slot="context-menu-group" {...props} />;
}

function ContextMenuPortal({ ...props }: React.ComponentProps<typeof ContextMenuPrimitive.Portal>) {
  return <ContextMenuPrimitive.Portal data-slot="context-menu-portal" {...props} />;
}

function ContextMenuSub({ ...props }: React.ComponentProps<typeof ContextMenuPrimitive.Sub>) {
  return <ContextMenuPrimitive.Sub data-slot="context-menu-sub" {...props} />;
}

function ContextMenuRadioGroup({
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.RadioGroup>) {
  return <ContextMenuPrimitive.RadioGroup data-slot="context-menu-radio-group" {...props} />;
}

const CONTEXT_MENU_CONTENT_CLASSES = [
  "z-50 max-h-(--radix-context-menu-content-available-height) min-w-36",
  "origin-(--radix-context-menu-content-transform-origin)",
  "overflow-x-hidden overflow-y-auto shadow-md",
  ...MENU_SURFACE_CLASSES,
];

function ContextMenuContent({
  className,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.Content>) {
  return (
    <ContextMenuPrimitive.Portal>
      <ContextMenuPrimitive.Content
        data-slot="context-menu-content"
        className={cn(CONTEXT_MENU_CONTENT_CLASSES, className)}
        {...props}
      />
    </ContextMenuPrimitive.Portal>
  );
}

const CONTEXT_MENU_SUB_TRIGGER_CLASSES = ["group/context-menu-item", ...MENU_SUB_TRIGGER_CLASSES];

function ContextMenuSubTrigger({
  className,
  inset,
  children,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.SubTrigger> & {
  inset?: boolean;
}) {
  return (
    <ContextMenuPrimitive.SubTrigger
      data-slot="context-menu-sub-trigger"
      data-inset={inset}
      className={cn(CONTEXT_MENU_SUB_TRIGGER_CLASSES, className)}
      {...props}
    >
      {children}
      <IconChevronRight className="ml-auto" />
    </ContextMenuPrimitive.SubTrigger>
  );
}

const CONTEXT_MENU_SUB_CONTENT_CLASSES = [
  "z-50 min-w-32",
  "origin-(--radix-context-menu-content-transform-origin) overflow-hidden shadow-lg",
  ...MENU_SURFACE_CLASSES,
];

function ContextMenuSubContent({
  className,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.SubContent>) {
  return (
    <ContextMenuPrimitive.SubContent
      data-slot="context-menu-sub-content"
      className={cn(CONTEXT_MENU_SUB_CONTENT_CLASSES, className)}
      {...props}
    />
  );
}

function ContextMenuLabel({
  className,
  inset,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.Label> & {
  inset?: boolean;
}) {
  return (
    <ContextMenuPrimitive.Label
      data-slot="context-menu-label"
      data-inset={inset}
      className={cn(MENU_LABEL_CLASSES, className)}
      {...props}
    />
  );
}

function ContextMenuSeparator({
  className,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.Separator>) {
  return (
    <ContextMenuPrimitive.Separator
      data-slot="context-menu-separator"
      className={cn(MENU_SEPARATOR_CLASSES, className)}
      {...props}
    />
  );
}

const CONTEXT_MENU_SHORTCUT_CLASSES = [
  MENU_SHORTCUT_CLASSES,
  "group-focus/context-menu-item:text-primary-subtle-foreground",
];

function ContextMenuShortcut({ className, ...props }: React.ComponentProps<"span">) {
  return (
    <span
      data-slot="context-menu-shortcut"
      className={cn(CONTEXT_MENU_SHORTCUT_CLASSES, className)}
      {...props}
    />
  );
}

export {
  ContextMenu,
  ContextMenuTrigger,
  ContextMenuContent,
  ContextMenuGroup,
  ContextMenuPortal,
  ContextMenuSub,
  ContextMenuSubTrigger,
  ContextMenuSubContent,
  ContextMenuRadioGroup,
  ContextMenuLabel,
  ContextMenuSeparator,
  ContextMenuShortcut,
};
