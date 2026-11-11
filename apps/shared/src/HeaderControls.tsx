import { ThemeToggle } from "@web-app-kit/frontend/ui";
import { ToolControls } from "./ToolControls";
import type { ToolControlsProps } from "./toolControls.types";

const THEME_LABELS = { system: "System", light: "Hell", dark: "Dunkel" };

export function HeaderControls(props: ToolControlsProps) {
  return (
    <>
      <ThemeToggle labels={THEME_LABELS} describe={(label) => `Farbschema: ${label}`} />
      <ToolControls {...props} />
    </>
  );
}
