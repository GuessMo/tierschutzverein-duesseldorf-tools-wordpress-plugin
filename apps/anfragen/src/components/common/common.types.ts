import type { ReactNode } from "react";
import type { Confirmation } from "@/types";

export interface HelpPopoverProps {
  text: string;
}

export interface ConfirmDialogProps {
  confirmation: Confirmation | null;
  onCancel: () => void;
  onConfirm: () => void;
}

export interface StateMessageProps {
  title: string;
  description?: string;
  action?: ReactNode;
}
