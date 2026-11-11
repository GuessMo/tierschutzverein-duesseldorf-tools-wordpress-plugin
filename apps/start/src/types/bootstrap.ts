export interface LauncherTool {
  slug: string;
  title: string;
  url: string;
  symbol: string;
}

export interface ToolBootstrap {
  restUrl: string;
  nonce: string;
  adminUrl: string;
  userName: string;
  userInitials: string;
  toolUrl: string;
  startUrl: string;
  logoutUrl: string;
  tools: LauncherTool[];
}

declare global {
  interface Window {
    tsvdToolBootstrap?: ToolBootstrap;
  }
}
