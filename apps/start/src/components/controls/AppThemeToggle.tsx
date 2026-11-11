import { ThemeToggle } from "@web-app-kit/frontend/ui";

const LABELS = { system: "System", light: "Hell", dark: "Dunkel" };

export function AppThemeToggle() {
  return <ThemeToggle labels={LABELS} describe={(label) => `Farbschema: ${label}`} />;
}
