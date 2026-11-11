export interface LauncherTool {
  slug: string;
  title: string;
  url: string;
  symbol: string;
  color: string;
}

export interface ToolControlsProps {
  tools: LauncherTool[];
  userName: string;
  userInitials: string;
  logoutUrl: string;
}
