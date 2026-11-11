import {
  IconAlertOctagon,
  IconAlertTriangle,
  IconCircleCheck,
  IconInfoCircle,
  IconLoader,
  IconX,
} from "@tabler/icons-react";
import { Toaster as Sonner } from "sonner";

import { useTheme } from "../ThemeProvider.js";
import { cn } from "./cn.js";
import { mergeToastClassNames } from "./sonner-classes.js";
import type { ToasterProps } from "./sonner.types.js";

function createToastIcons() {
  return {
    success: <IconCircleCheck aria-hidden className="size-4" />,
    info: <IconInfoCircle aria-hidden className="size-4" />,
    warning: <IconAlertTriangle aria-hidden className="size-4" />,
    error: <IconAlertOctagon aria-hidden className="size-4" />,
    loading: <IconLoader aria-hidden className="size-4 animate-spin motion-reduce:animate-none" />,
    close: <IconX aria-hidden />,
  };
}

function Toaster({
  closeLabel,
  regionLabel,
  closeButton = true,
  className,
  toastOptions,
  ...props
}: ToasterProps) {
  const { resolvedTheme } = useTheme();
  return (
    <Sonner
      theme={resolvedTheme}
      className={cn("toaster group", className)}
      icons={createToastIcons()}
      containerAriaLabel={regionLabel}
      closeButton={closeButton}
      toastOptions={{
        ...toastOptions,
        unstyled: true,
        closeButtonAriaLabel: closeLabel,
        classNames: mergeToastClassNames(toastOptions?.classNames),
      }}
      {...props}
    />
  );
}

export { Toaster };
export { toast } from "sonner";
