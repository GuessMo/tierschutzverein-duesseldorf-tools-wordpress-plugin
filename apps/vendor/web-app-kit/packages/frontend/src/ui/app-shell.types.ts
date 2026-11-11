import type { ReactNode } from "react";

type NavigationSlot =
  | { navigation?: null; navigationLabel?: never }
  | { navigation: ReactNode; navigationLabel: string };

type DetailSlot =
  | { detail?: null; detailLabel?: never }
  | { detail: ReactNode; detailLabel: string };

export interface AppShellLinkProps {
  href: string;
  className: string;
  "data-slot": string;
  children: ReactNode;
}

export interface AppShellHeaderProps {
  brand: ReactNode;
  homeHref?: string;
  renderLink?: (props: AppShellLinkProps) => ReactNode;
  actions?: ReactNode;
  controls?: ReactNode;
}

export type AppShellBrandProps = Pick<AppShellHeaderProps, "brand" | "homeHref" | "renderLink">;

export type AppShellPanelPosition = "start" | "end";

export interface AppShellPanelProps {
  as: "nav" | "aside";
  label: string;
  position: AppShellPanelPosition;
  children: ReactNode;
}

export interface AppShellBodyProps {
  navigation?: ReactNode;
  navigationLabel?: string;
  detail?: ReactNode;
  detailLabel?: string;
  children: ReactNode;
}

export type AppShellProps = AppShellHeaderProps & {
  skipLinkLabel: string;
  footer?: ReactNode;
  children: ReactNode;
} & NavigationSlot &
  DetailSlot;
