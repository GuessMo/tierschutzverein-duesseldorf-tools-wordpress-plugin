import type { ComponentProps } from "react";
import type { Theme } from "../ThemeProvider.js";
import type { Button } from "./button.js";

export type ThemeToggleLabels = Record<Theme, string>;

export interface ThemeToggleProps extends Omit<
  ComponentProps<typeof Button>,
  "children" | "onClick"
> {
  labels: ThemeToggleLabels;
  describe: (themeLabel: string) => string;
}
