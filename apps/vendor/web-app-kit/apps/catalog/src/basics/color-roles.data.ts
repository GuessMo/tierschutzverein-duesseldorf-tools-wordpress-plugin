import type { ColorRole, ColorRoleGroup } from "./basics.types";

const TEXT = 4.5;
const LINE = 3;

function text(name: string, className: string): ColorRole {
  return { name, className, kind: "text", minimum: TEXT };
}

function line(name: string, className: string, minimum: number | null): ColorRole {
  return { name, className, kind: "line", minimum };
}

export const COLOR_ROLE_GROUPS: ColorRoleGroup[] = [
  {
    id: "surfaces",
    title: "Flächen",
    roles: [
      text("background", "bg-background text-foreground"),
      text("card", "bg-card text-card-foreground"),
      text("popover", "bg-popover text-popover-foreground"),
      text("muted", "bg-muted text-muted-foreground"),
      text("accent", "bg-accent text-accent-foreground"),
    ],
  },
  {
    id: "primary",
    title: "Primär",
    roles: [
      text("primary", "bg-primary text-primary-foreground"),
      text("primary-hover", "bg-primary-hover text-primary-foreground"),
      text("primary-active", "bg-primary-active text-primary-foreground"),
      text("primary-subtle", "bg-primary-subtle text-primary-subtle-foreground"),
    ],
  },
  {
    id: "secondary",
    title: "Sekundär",
    roles: [
      text("secondary", "bg-secondary text-secondary-foreground"),
      text("secondary-hover", "bg-secondary-hover text-secondary-foreground"),
      text("secondary-active", "bg-secondary-active text-secondary-foreground"),
      text("secondary-subtle", "bg-secondary-subtle text-secondary-subtle-foreground"),
    ],
  },
  {
    id: "tertiary",
    title: "Tertiär",
    roles: [
      text("tertiary", "bg-tertiary text-tertiary-foreground"),
      text("tertiary-hover", "bg-tertiary-hover text-tertiary-foreground"),
      text("tertiary-active", "bg-tertiary-active text-tertiary-active-foreground"),
    ],
  },
  {
    id: "text",
    title: "Text, Status und Instanz",
    roles: [
      text("muted-foreground", "bg-background text-muted-foreground"),
      text("destructive", "bg-background text-destructive"),
      text("brand", "bg-brand text-brand-foreground"),
    ],
  },
  {
    id: "sidebar",
    title: "Seitenleiste",
    roles: [
      text("sidebar", "bg-sidebar text-sidebar-foreground"),
      text("sidebar-primary", "bg-sidebar-primary text-sidebar-primary-foreground"),
      text("sidebar-accent", "bg-sidebar-accent text-sidebar-accent-foreground"),
    ],
  },
  {
    id: "lines",
    title: "Linien und Fokus",
    roles: [
      line("input", "bg-background border-input", LINE),
      line("ring", "bg-background border-ring", LINE),
      line("border", "bg-background border-border", null),
    ],
  },
];
