import type { ReactNode } from "react";
import type { ToneId } from "@/types/document";

export interface FieldRowProps {
  label: string;
  htmlFor?: string;
  children: ReactNode;
}

export interface OptionItem<T extends string> {
  value: T;
  label: string;
}

export interface OptionGroupProps<T extends string> {
  label: string;
  value: T;
  options: OptionItem<T>[];
  onChange: (value: T) => void;
}

export interface ToneSwatchesProps {
  label: string;
  value: ToneId;
  onChange: (value: ToneId) => void;
  allowTransparent?: boolean;
}

export interface PanelSectionProps {
  title: string;
  children: ReactNode;
}
