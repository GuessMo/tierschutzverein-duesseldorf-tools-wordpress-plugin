import * as React from "react";
import { AlertDialog as AlertDialogPrimitive } from "radix-ui";

import { cn } from "./cn.js";

const ALERT_DIALOG_HEADER_CLASSES = [
  "grid grid-rows-[auto_1fr] place-items-center gap-1.5 text-center",
  "has-data-[slot=alert-dialog-media]:grid-rows-[auto_auto_1fr]",
  "has-data-[slot=alert-dialog-media]:gap-x-4",
  "sm:group-data-[size=default]/alert-dialog-content:place-items-start",
  "sm:group-data-[size=default]/alert-dialog-content:text-left",
  "sm:group-data-[size=default]/alert-dialog-content:has-data-[slot=alert-dialog-media]:grid-rows-[auto_1fr]",
];

function AlertDialogHeader({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="alert-dialog-header"
      className={cn(ALERT_DIALOG_HEADER_CLASSES, className)}
      {...props}
    />
  );
}

const ALERT_DIALOG_FOOTER_CLASSES = [
  "-mx-4 -mb-4 flex flex-col-reverse gap-2 rounded-b-xl border-t bg-muted/50 p-4",
  "group-data-[size=sm]/alert-dialog-content:grid",
  "group-data-[size=sm]/alert-dialog-content:grid-cols-2 sm:flex-row sm:justify-end",
];

function AlertDialogFooter({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="alert-dialog-footer"
      className={cn(ALERT_DIALOG_FOOTER_CLASSES, className)}
      {...props}
    />
  );
}

const ALERT_DIALOG_MEDIA_CLASSES = [
  "mb-2 inline-flex size-10 items-center justify-center rounded-md bg-muted",
  "sm:group-data-[size=default]/alert-dialog-content:row-span-2",
  "*:[svg:not([class*='size-'])]:size-6",
];

function AlertDialogMedia({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="alert-dialog-media"
      className={cn(ALERT_DIALOG_MEDIA_CLASSES, className)}
      {...props}
    />
  );
}

const ALERT_DIALOG_TITLE_CLASSES = [
  "font-heading text-base font-medium",
  "sm:group-data-[size=default]/alert-dialog-content:group-has-data-[slot=alert-dialog-media]/alert-dialog-content:col-start-2",
];

function AlertDialogTitle({
  className,
  ...props
}: React.ComponentProps<typeof AlertDialogPrimitive.Title>) {
  return (
    <AlertDialogPrimitive.Title
      data-slot="alert-dialog-title"
      className={cn(ALERT_DIALOG_TITLE_CLASSES, className)}
      {...props}
    />
  );
}

const ALERT_DIALOG_DESCRIPTION_CLASSES = [
  "text-sm text-balance text-muted-foreground md:text-pretty *:[a]:underline",
  "*:[a]:underline-offset-3 *:[a]:hover:text-foreground",
];

function AlertDialogDescription({
  className,
  ...props
}: React.ComponentProps<typeof AlertDialogPrimitive.Description>) {
  return (
    <AlertDialogPrimitive.Description
      data-slot="alert-dialog-description"
      className={cn(ALERT_DIALOG_DESCRIPTION_CLASSES, className)}
      {...props}
    />
  );
}

export {
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogMedia,
  AlertDialogTitle,
};
