import { AccountMenu, AppLauncher, type AppLauncherItem } from "@web-app-kit/frontend/ui";
import { symbolIcon } from "./symbols";
import type { LauncherTool, ToolControlsProps } from "./toolControls.types";

function launcherItems(tools: LauncherTool[]): AppLauncherItem[] {
  return tools.map((tool) => ({
    id: tool.slug,
    label: tool.title,
    href: tool.url,
    icon: symbolIcon(tool.symbol, tool.color),
  }));
}

export function ToolControls({ tools, userName, userInitials, logoutUrl }: ToolControlsProps) {
  return (
    <>
      <AppLauncher items={launcherItems(tools)} label="Tools" newTabHint="(öffnet in neuem Tab)" />
      <AccountMenu
        name={userName}
        initials={userInitials}
        label={`Konto: ${userName}`}
        logoutLabel="Abmelden"
        onLogout={() => window.location.assign(logoutUrl)}
      />
    </>
  );
}
