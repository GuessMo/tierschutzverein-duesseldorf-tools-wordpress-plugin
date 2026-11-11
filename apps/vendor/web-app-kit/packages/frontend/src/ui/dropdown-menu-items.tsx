import * as React from "react";
import { DropdownMenu as DropdownMenuPrimitive } from "radix-ui";
import { IconCheck } from "@tabler/icons-react";
import { cn } from "./cn.js";
import {
  MENU_CHECK_INDICATOR_CLASSES,
  MENU_ITEM_CLASSES,
  MENU_SELECTABLE_ITEM_CLASSES,
} from "./menu-classes.js";

const DROPDOWN_MENU_ITEM_CLASSES = ["group/dropdown-menu-item", ...MENU_ITEM_CLASSES];

function ItemCheckIndicator({ slot }: { slot: string }) {
  return (
    <span
      className={cn(MENU_CHECK_INDICATOR_CLASSES)}
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
      className={cn(MENU_SELECTABLE_ITEM_CLASSES, className)}
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
      className={cn(MENU_SELECTABLE_ITEM_CLASSES, className)}
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
