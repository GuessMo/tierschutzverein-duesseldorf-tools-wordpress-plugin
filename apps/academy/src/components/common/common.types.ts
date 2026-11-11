import type { ReactNode } from "react";

export interface StateMessageProps {
  title: string;
  description?: string;
  action?: ReactNode;
}
