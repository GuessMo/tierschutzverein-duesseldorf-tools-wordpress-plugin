import * as React from "react";
import { DropdownMenu as DropdownMenuPrimitive } from "radix-ui";
import { IconCheck } from "@tabler/icons-react";
import { cn } from "./cn.js";

const DROPDOWN_MENU_ITEM_CLASSES = [
  "focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset",
  "group/dropdown-menu-item relative flex cursor-default items-center",
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

function ItemCheckIndicator({ slot }: { slot: string }) {
  return (
    <span
      className={cn([
        "pointer-events-none absolute right-2 flex items-center justify-center",
        "text-primary",
      ])}
      data-slot={slot}
    >
      <DropdownMenuPrimitive.ItemIndicator>
        <IconCheck />
      </DropdownMenuPrimitive.ItemIndicator>
    </span>
  );
}

function DropdownMenuItem({
  className,
  inset,
  variant = "default",
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.Item> & {
  inset?: boolean;
  variant?: "default" | "destructive";
}) {
  return (
    <DropdownMenuPrimitive.Item
      data-slot="dropdown-menu-item"
      data-inset={inset}
      data-variant={variant}
      className={cn(DROPDOWN_MENU_ITEM_CLASSES, className)}
      {...props}
    />
  );
}

const SELECTABLE_ITEM_CLASSES = [
  "focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset",
  "relative flex cursor-default items-center gap-1.5 rounded-md py-1 pr-8",
  "pl-1.5 text-sm outline-hidden select-none focus:bg-primary-subtle",
  "focus:text-primary-subtle-foreground",
  "data-inset:pl-7 data-disabled:pointer-events-none",
  "data-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
  "[&_svg:not([class*='size-'])]:size-4",
];

function DropdownMenuCheckboxItem({
  className,
  children,
  checked,
  inset,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.CheckboxItem> & {
  inset?: boolean;
}) {
  return (
    <DropdownMenuPrimitive.CheckboxItem
      data-slot="dropdown-menu-checkbox-item"
      data-inset={inset}
      className={cn(SELECTABLE_ITEM_CLASSES, className)}
      checked={checked}
      {...props}
    >
      <ItemCheckIndicator slot="dropdown-menu-checkbox-item-indicator" />
      {children}
    </DropdownMenuPrimitive.CheckboxItem>
  );
}

function DropdownMenuRadioGroup({
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.RadioGroup>) {
  return <DropdownMenuPrimitive.RadioGroup data-slot="dropdown-menu-radio-group" {...props} />;
}

function DropdownMenuRadioItem({
  className,
  children,
  inset,
  ...props
}: React.ComponentProps<typeof DropdownMenuPrimitive.RadioItem> & {
  inset?: boolean;
}) {
  return (
    <DropdownMenuPrimitive.RadioItem
      data-slot="dropdown-menu-radio-item"
      data-inset={inset}
      className={cn(SELECTABLE_ITEM_CLASSES, className)}
      {...props}
    >
      <ItemCheckIndicator slot="dropdown-menu-radio-item-indicator" />
      {children}
    </DropdownMenuPrimitive.RadioItem>
  );
}

export {
  DropdownMenuItem,
  DropdownMenuCheckboxItem,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
};
