import type { ComponentProps, ReactNode } from "react";
import type { ValueLabel } from "@/types";

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

export type TextFieldProps = Omit<ComponentProps<"input">, "id" | "value" | "onChange"> & {
  id: string;
  label: string;
  value: string;
  hint?: string;
  onValueChange: (value: string) => void;
};

export interface TextAreaFieldProps {
  id: string;
  label: string;
  value: string;
  rows?: number;
  hint?: string;
  onValueChange: (value: string) => void;
}

export interface SelectFieldProps {
  id: string;
  label: string;
  value: string;
  options: ValueLabel[];
  hint?: string;
  disabled?: boolean;
  onValueChange: (value: string) => void;
}

export interface CheckboxFieldProps {
  id: string;
  label: string;
  checked: boolean;
  hint?: string;
  onCheckedChange: (checked: boolean) => void;
}

export interface CopyValueProps {
  label: string;
  value: string;
}

export interface FormSectionProps {
  id: string;
  title: string;
  description?: string;
  children: ReactNode;
}
