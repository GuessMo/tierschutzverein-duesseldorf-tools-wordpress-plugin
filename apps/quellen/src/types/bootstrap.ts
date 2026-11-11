export interface ToolBootstrap {
  restUrl: string;
  nonce: string;
  adminUrl: string;
  userName: string;
  toolUrl: string;
}

declare global {
  interface Window {
    tsvdToolBootstrap?: ToolBootstrap;
  }
}
