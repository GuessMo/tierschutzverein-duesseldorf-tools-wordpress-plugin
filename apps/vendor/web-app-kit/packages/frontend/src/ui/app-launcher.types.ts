import type { ReactNode } from "react";

export interface AppLauncherItem {
  id: string;
  label: string;
  href: string;
  icon: ReactNode;
}

export interface AppLauncherProps {
  items: AppLauncherItem[];
  label: string;
  newTabHint: string;
}
