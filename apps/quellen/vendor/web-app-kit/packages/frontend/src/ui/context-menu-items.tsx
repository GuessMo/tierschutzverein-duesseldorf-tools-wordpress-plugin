import * as React from "react";
import { ContextMenu as ContextMenuPrimitive } from "radix-ui";
import { IconCheck } from "@tabler/icons-react";
import { cn } from "./cn.js";
import {
  MENU_CHECK_INDICATOR_CLASSES,
  MENU_ITEM_CLASSES,
  MENU_SELECTABLE_ITEM_CLASSES,
} from "./menu-classes.js";

const CONTEXT_MENU_ITEM_CLASSES = ["group/context-menu-item", ...MENU_ITEM_CLASSES];

function ItemCheckIndicator({ slot }: { slot: string }) {
  return (
    <span className={cn(MENU_CHECK_INDICATOR_CLASSES)} data-slot={slot}>
      <ContextMenuPrimitive.ItemIndicator>
        <IconCheck />
      </ContextMenuPrimitive.ItemIndicator>
    </span>
  );
}

function ContextMenuItem({
  className,
  inset,
  variant = "default",
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.Item> & {
  inset?: boolean;
  variant?: "default" | "destructive";
}) {
  return (
    <ContextMenuPrimitive.Item
      data-slot="context-menu-item"
      data-inset={inset}
      data-variant={variant}
      className={cn(CONTEXT_MENU_ITEM_CLASSES, className)}
      {...props}
    />
  );
}

function ContextMenuCheckboxItem({
  className,
  children,
  checked,
  inset,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.CheckboxItem> & {
  inset?: boolean;
}) {
  return (
    <ContextMenuPrimitive.CheckboxItem
      data-slot="context-menu-checkbox-item"
      data-inset={inset}
      className={cn(MENU_SELECTABLE_ITEM_CLASSES, className)}
      checked={checked}
      {...props}
    >
      <ItemCheckIndicator slot="context-menu-checkbox-item-indicator" />
      {children}
    </ContextMenuPrimitive.CheckboxItem>
  );
}

function ContextMenuRadioItem({
  className,
  children,
  inset,
  ...props
}: React.ComponentProps<typeof ContextMenuPrimitive.RadioItem> & {
  inset?: boolean;
}) {
  return (
    <ContextMenuPrimitive.RadioItem
      data-slot="context-menu-radio-item"
      data-inset={inset}
      className={cn(MENU_SELECTABLE_ITEM_CLASSES, className)}
      {...props}
    >
      <ItemCheckIndicator slot="context-menu-radio-item-indicator" />
      {children}
    </ContextMenuPrimitive.RadioItem>
  );
}

export { ContextMenuItem, ContextMenuCheckboxItem, ContextMenuRadioItem };
