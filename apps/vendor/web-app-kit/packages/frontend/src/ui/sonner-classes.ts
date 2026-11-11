import type { ToastClassnames } from "sonner";

import { buttonVariants } from "./button.js";
import { cn } from "./cn.js";

const TOAST_CLASSES = [
  "group/toast flex w-(--width) items-start gap-2 rounded-xl bg-popover p-4 text-sm",
  "text-popover-foreground shadow-lg ring-1 ring-foreground/10",
  "has-[[data-close-button]]:pr-10",
  "data-[expanded=false]:data-[front=false]:*:opacity-0",
];

const TOAST_FOCUS_CLASSES = [
  "focus-visible:outline-2! focus-visible:outline-offset-2! focus-visible:outline-solid!",
  "focus-visible:outline-ring!",
];

const TOAST_CLOSE_CLASSES = [
  "absolute top-2 right-2 border-transparent! bg-transparent! text-popover-foreground!",
  "not-disabled:hover:bg-primary-subtle! not-disabled:hover:text-primary-subtle-foreground!",
  "not-disabled:active:border-primary! not-disabled:active:bg-primary-subtle!",
  "focus-visible:border-ring!",
];

export const TOAST_CLASS_NAMES: ToastClassnames = {
  toast: cn(TOAST_CLASSES, TOAST_FOCUS_CLASSES),
  content: "flex min-w-0 flex-1 flex-col gap-0.5",
  title: "leading-5 font-medium",
  description: "text-muted-foreground!",
  icon: "mt-0.5 flex size-4 shrink-0 items-center",
  actionButton: buttonVariants({ size: "sm" }),
  cancelButton: buttonVariants({ variant: "outline", size: "sm" }),
  closeButton: cn(buttonVariants({ variant: "ghost", size: "icon-xs" }), TOAST_CLOSE_CLASSES),
};

export function mergeToastClassNames(custom: ToastClassnames = {}): ToastClassnames {
  const keys = new Set([...Object.keys(TOAST_CLASS_NAMES), ...Object.keys(custom)]);
  return Object.fromEntries(
    [...keys].map((key) => {
      const name = key as keyof ToastClassnames;
      return [name, cn(TOAST_CLASS_NAMES[name], custom[name])];
    }),
  );
}
