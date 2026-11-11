import { IconDeviceDesktop, IconMoon, IconSun } from "@tabler/icons-react";
import { useTheme, type Theme } from "../ThemeProvider.js";
import { Button } from "./button.js";
import type { ThemeToggleProps } from "./theme-toggle.types.js";

const NEXT_THEME: Record<Theme, Theme> = { system: "light", light: "dark", dark: "system" };

const THEME_ICONS = { system: IconDeviceDesktop, light: IconSun, dark: IconMoon };

function toKnownTheme(theme: string): Theme {
  return theme === "light" || theme === "dark" ? theme : "system";
}

export function ThemeToggle({
  labels,
  describe,
  variant = "ghost",
  size = "icon",
  ...props
}: ThemeToggleProps) {
  const { theme, setTheme } = useTheme();
  const current = toKnownTheme(theme);
  const Icon = THEME_ICONS[current];
  const label = describe(labels[current]);
  return (
    <Button
      type="button"
      variant={variant}
      size={size}
      aria-label={label}
      title={label}
      onClick={() => setTheme(NEXT_THEME[current])}
      {...props}
    >
      <Icon />
    </Button>
  );
}
