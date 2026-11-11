import * as React from "react";
import { DropdownMenu as DropdownMenuPrimitive } from "radix-ui";
import { IconChevronRight } from "@tabler/icons-react";
import { cn } from "./cn.js";
import {
  MENU_LABEL_CLASSES,
  MENU_SEPARATOR_CLASSES,
  MENU_SHORTCUT_CLASSES,
  MENU_SUB_TRIGGER_CLASSES,
  MENU_SURFACE_CLASSES,
} from "./menu-classes.js";

function DropdownMenu({ ...props }: React.ComponentProps<typeof DropdownMenuPrimitive.Root>) {
  return <DropdownMenuPrimitive.Root data-slot="dropdown-menu" {...props} />;
}

function DropdownMenuPortal({
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.Portal>) {
  return <DropdownMenuPrimitive.Portal data-slot="dropdown-menu-portal" {...props} />;
}

function DropdownMenuTrigger({
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.Trigger>) {
  return <DropdownMenuPrimitive.Trigger data-slot="dropdown-menu-trigger" {...props} />;
}

const DROPDOWN_MENU_CONTENT_CLASSES = [
  "z-50 max-h-(--radix-dropdown-menu-content-available-height)",
  "w-(--radix-dropdown-menu-trigger-width) min-w-32",
  "origin-(--radix-dropdown-menu-content-transform-origin)",
  "overflow-x-hidden overflow-y-auto shadow-md data-[state=closed]:overflow-hidden",
  ...MENU_SURFACE_CLASSES,
];

function DropdownMenuContent({
  className,
  align = "start",
  sideOffset = 4,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.Content>) {
  return (
    <DropdownMenuPrimitive.Portal>
      <DropdownMenuPrimitive.Content
        data-slot="dropdown-menu-content"
        sideOffset={sideOffset}
        align={align}
        className={cn(DROPDOWN_MENU_CONTENT_CLASSES, className)}
        {...props}
      />
    </DropdownMenuPrimitive.Portal>
  );
}

function DropdownMenuGroup({ ...props }: React.ComponentProps<typeof DropdownMenuPrimitive.Group>) {
  return <DropdownMenuPrimitive.Group data-slot="dropdown-menu-group" {...props} />;
}

function DropdownMenuLabel({
  className,
  inset,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.Label> & {
  inset?: boolean;
}) {
  return (
    <DropdownMenuPrimitive.Label
      data-slot="dropdown-menu-label"
      data-inset={inset}
      className={cn(MENU_LABEL_CLASSES, className)}
      {...props}
    />
  );
}

function DropdownMenuSeparator({
  className,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.Separator>) {
  return (
    <DropdownMenuPrimitive.Separator
      data-slot="dropdown-menu-separator"
      className={cn(MENU_SEPARATOR_CLASSES, className)}
      {...props}
    />
  );
}

const DROPDOWN_MENU_SHORTCUT_CLASSES = [
  MENU_SHORTCUT_CLASSES,
  "group-focus/dropdown-menu-item:text-primary-subtle-foreground",
];

function DropdownMenuShortcut({ className, ...props }: React.ComponentProps<"span">) {
  return (
    <span
      data-slot="dropdown-menu-shortcut"
      className={cn(DROPDOWN_MENU_SHORTCUT_CLASSES, className)}
      {...props}
    />
  );
}

function DropdownMenuSub({ ...props }: React.ComponentProps<typeof DropdownMenuPrimitive.Sub>) {
  return <DropdownMenuPrimitive.Sub data-slot="dropdown-menu-sub" {...props} />;
}

const DROPDOWN_MENU_SUB_TRIGGER_CLASSES = ["group/dropdown-menu-item", ...MENU_SUB_TRIGGER_CLASSES];

function DropdownMenuSubTrigger({
  className,
  inset,
  children,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.SubTrigger> & {
  inset?: boolean;
}) {
  return (
    <DropdownMenuPrimitive.SubTrigger
      data-slot="dropdown-menu-sub-trigger"
      data-inset={inset}
      className={cn(DROPDOWN_MENU_SUB_TRIGGER_CLASSES, className)}
      {...props}
    >
      {children}
      <IconChevronRight className="ml-auto" />
    </DropdownMenuPrimitive.SubTrigger>
  );
}

const DROPDOWN_MENU_SUB_CONTENT_CLASSES = [
  "z-50 min-w-[96px]",
  "origin-(--radix-dropdown-menu-content-transform-origin) overflow-hidden shadow-lg",
  ...MENU_SURFACE_CLASSES,
];

function DropdownMenuSubContent({
  className,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.SubContent>) {
  return (
    <DropdownMenuPrimitive.SubContent
      data-slot="dropdown-menu-sub-content"
      className={cn(DROPDOWN_MENU_SUB_CONTENT_CLASSES, className)}
      {...props}
    />
  );
}

export {
  DropdownMenu,
  DropdownMenuPortal,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuSub,
  DropdownMenuSubTrigger,
  DropdownMenuSubContent,
};
