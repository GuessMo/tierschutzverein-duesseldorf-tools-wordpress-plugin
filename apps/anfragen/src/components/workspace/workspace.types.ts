import type { ReactNode } from "react";
import type { AnfragenContext } from "@/types";

export interface WorkspaceProps {
  context: AnfragenContext;
  nav: ReactNode;
}

export interface StartupScreenProps {
  error: string | null;
  onRetry: () => Promise<void>;
}
