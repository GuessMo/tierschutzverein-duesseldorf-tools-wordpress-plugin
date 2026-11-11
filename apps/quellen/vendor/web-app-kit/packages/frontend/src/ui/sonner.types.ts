import type { ToasterProps as SonnerToasterProps } from "sonner";

export type ToasterProps = Omit<
  SonnerToasterProps,
  "theme" | "icons" | "containerAriaLabel" | "customAriaLabel"
> & {
  closeLabel: string;
  regionLabel: string;
};
