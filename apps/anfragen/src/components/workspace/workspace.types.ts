import type { AnfragenContext } from "@/types";

export interface WorkspaceProps {
  context: AnfragenContext;
}

export interface StartupScreenProps {
  error: string | null;
  onRetry: () => Promise<void>;
}
