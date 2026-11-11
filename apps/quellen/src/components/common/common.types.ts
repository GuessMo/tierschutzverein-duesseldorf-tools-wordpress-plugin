import type { ComponentProps, ReactNode } from "react";

export interface FormDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title: string;
  description?: string;
  submitLabel: string;
  onSubmit: () => Promise<void>;
  children: ReactNode;
}

export interface ConfirmDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title: string;
  description: string;
  confirmLabel: string;
  onConfirm: () => Promise<void>;
}

export interface StateMessageProps {
  title: string;
  description?: string;
  action?: ReactNode;
}

export interface FieldProps {
  id: string;
  label: string;
  hint?: string;
  children: ReactNode;
}

export interface DialogHeadingProps {
  title: string;
  description?: string;
}

export type TextFieldProps = Omit<ComponentProps<"input">, "id" | "value" | "onChange"> & {
  id: string;
  label: string;
  value: string;
  onValueChange: (value: string) => void;
};

export interface NameFieldProps {
  id: string;
  value: string;
  onValueChange: (value: string) => void;
}

export interface EditDeleteActionsProps {
  name: string;
  onEdit: () => void;
  onDelete: () => void;
}
