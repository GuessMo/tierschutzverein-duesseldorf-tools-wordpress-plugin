export interface Confirmation {
  title: string;
  description: string;
  confirmLabel: string;
  isDestructive: boolean;
}

export interface PendingConfirmation {
  confirmation: Confirmation;
  run: () => Promise<unknown>;
}

export type AskConfirmation = (confirmation: Confirmation, run: () => Promise<unknown>) => void;
