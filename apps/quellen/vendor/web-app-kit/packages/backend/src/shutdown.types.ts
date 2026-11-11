export interface ShutdownOptions {
  onClose?: () => Promise<void> | void;
  timeoutMs?: number;
  exit?: (code: number) => void;
}

export type UnregisterShutdown = () => void;
