import type { ComponentProps } from "react";
import type { Dialog } from "./dialog.js";

export type CommandDialogProps = ComponentProps<typeof Dialog> & {
  title: string;
  description: string;
  className?: string;
  closeLabel?: string;
};
