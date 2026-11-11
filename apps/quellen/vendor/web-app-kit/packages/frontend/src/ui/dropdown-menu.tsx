import * as React from "react";
import { DropdownMenu as DropdownMenuPrimitive } from "radix-ui";
import { IconChevronRight } from "@tabler/icons-react";
import { cn } from "./cn.js";

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
  "overflow-x-hidden overflow-y-auto rounded-lg bg-popover p-1",
  "text-popover-foreground shadow-md ring-1 ring-foreground/10 duration-100",
  "data-[side=bottom]:slide-in-from-top-2",
  "data-[side=left]:slide-in-from-right-2",
  "data-[side=right]:slide-in-from-left-2",
  "data-[side=top]:slide-in-from-bottom-2",
  "data-[state=closed]:overflow-hidden data-open:animate-in",
  "data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out",
  "data-closed:fade-out-0 data-closed:zoom-out-95",
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
      className={cn(
        "px-1.5 py-1 text-xs font-medium text-muted-foreground data-inset:pl-7",
        className,
      )}
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
      className={cn("-mx-1 my-1 h-px bg-border", className)}
      {...props}
    />
  );
}

const DROPDOWN_MENU_SHORTCUT_CLASSES = [
  "ml-auto text-xs tracking-widest text-muted-foreground",
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

const DROPDOWN_MENU_SUB_TRIGGER_CLASSES = [
  "group/dropdown-menu-item",
  "focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset",
  "flex cursor-default items-center gap-1.5 rounded-md px-1.5 py-1 text-sm",
  "outline-hidden select-none focus:bg-primary-subtle",
  "focus:text-primary-subtle-foreground",
  "not-data-[variant=destructive]:focus:**:text-primary-subtle-foreground",
  "data-inset:pl-7 data-open:bg-primary-subtle data-open:text-primary-subtle-foreground",
  "[&_svg]:pointer-events-none [&_svg]:shrink-0",
  "[&_svg:not([class*='size-'])]:size-4",
];

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
  "origin-(--radix-dropdown-menu-content-transform-origin) overflow-hidden",
  "rounded-lg bg-popover p-1 text-popover-foreground shadow-lg ring-1",
  "ring-foreground/10 duration-100 data-[side=bottom]:slide-in-from-top-2",
  "data-[side=left]:slide-in-from-right-2",
  "data-[side=right]:slide-in-from-left-2",
  "data-[side=top]:slide-in-from-bottom-2 data-open:animate-in",
  "data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out",
  "data-closed:fade-out-0 data-closed:zoom-out-95",
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
