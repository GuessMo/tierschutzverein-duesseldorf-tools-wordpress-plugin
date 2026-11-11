import * as React from "react";
import { IconCheck, IconSearch } from "@tabler/icons-react";
import { Command as CommandPrimitive } from "cmdk";

import { cn } from "./cn.js";
import {
  COMMAND_CLASSES,
  COMMAND_INPUT_GROUP_CLASSES,
  COMMAND_LIST_CLASSES,
  COMMAND_GROUP_CLASSES,
  COMMAND_ITEM_CLASSES,
  COMMAND_ITEM_KIT_CLASSES,
  COMMAND_CHECK_CLASSES,
  COMMAND_SHORTCUT_CLASSES,
} from "./command-classes.js";
import type { CommandDialogProps } from "./command.types.js";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "./dialog.js";
import { InputGroup, InputGroupAddon } from "./input-group.js";

function Command({ className, ...props }: React.ComponentProps<typeof CommandPrimitive>) {
  return (
    <CommandPrimitive
      data-slot="command"
      className={cn(COMMAND_CLASSES, className)}
      {...props}
    />
  );
}

function CommandDialog({
  title,
  description,
  children,
  className,
  closeLabel,
  ...props
}: CommandDialogProps) {
  const closeButton = closeLabel
    ? { showCloseButton: true as const, closeLabel }
    : { showCloseButton: false as const };
  return (
    <Dialog {...props}>
      <DialogHeader className="sr-only">
        <DialogTitle>{title}</DialogTitle>
        <DialogDescription>{description}</DialogDescription>
      </DialogHeader>
      <DialogContent
        className={cn("top-1/3 translate-y-0 overflow-hidden rounded-xl! p-0", className)}
        {...closeButton}
      >
        {children}
      </DialogContent>
    </Dialog>
  );
}

function CommandInput({
  className,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.Input>) {
  return (
    <div data-slot="command-input-wrapper" className="p-1 pb-0">
      <InputGroup className={cn(COMMAND_INPUT_GROUP_CLASSES)}>
        <CommandPrimitive.Input
          data-slot="command-input"
          className={cn(
            "w-full text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50",
            className,
          )}
          {...props}
        />
        <InputGroupAddon>
          <IconSearch aria-hidden className="size-4 shrink-0 opacity-50" />
        </InputGroupAddon>
      </InputGroup>
    </div>
  );
}

function CommandList({
  className,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.List>) {
  return (
    <CommandPrimitive.List
      data-slot="command-list"
      className={cn(COMMAND_LIST_CLASSES, className)}
      {...props}
    />
  );
}

function CommandEmpty({
  className,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.Empty>) {
  return (
    <CommandPrimitive.Empty
      data-slot="command-empty"
      className={cn("py-6 text-center text-sm", className)}
      {...props}
    />
  );
}

function CommandGroup({
  className,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.Group>) {
  return (
    <CommandPrimitive.Group
      data-slot="command-group"
      className={cn(COMMAND_GROUP_CLASSES, className)}
      {...props}
    />
  );
}

function CommandSeparator({
  className,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.Separator>) {
  return (
    <CommandPrimitive.Separator
      data-slot="command-separator"
      className={cn("-mx-1 h-px bg-border", className)}
      {...props}
    />
  );
}

function CommandItem({
  className,
  children,
  ...props
}: React.ComponentProps<typeof CommandPrimitive.Item>) {
  return (
    <CommandPrimitive.Item
      data-slot="command-item"
      className={cn(COMMAND_ITEM_CLASSES, COMMAND_ITEM_KIT_CLASSES, className)}
      {...props}
    >
      {children}
      <IconCheck aria-hidden className={cn(COMMAND_CHECK_CLASSES)} />
    </CommandPrimitive.Item>
  );
}

function CommandShortcut({ className, ...props }: React.ComponentProps<"span">) {
  return (
    <span
      data-slot="command-shortcut"
      className={cn(COMMAND_SHORTCUT_CLASSES, className)}
      {...props}
    />
  );
}

export {
  Command,
  CommandDialog,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
  CommandShortcut,
};
