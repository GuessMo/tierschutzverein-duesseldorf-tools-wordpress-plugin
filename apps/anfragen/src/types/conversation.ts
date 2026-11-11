export type MessageDirection = "in" | "out" | "note";

export type ComposerMode = "reply" | "note";

export interface Animal {
  id: number;
  name: string;
  thumbUrl: string;
  facts: string[];
  editUrl: string;
  viewUrl: string;
}

export interface Fact {
  label: string;
  value: string;
}

export interface Message {
  id: number;
  direction: MessageDirection;
  author: string;
  stamp: string;
  isEdited: boolean;
  body: string;
  isPending: boolean;
  remaining: number;
  isMutable: boolean;
}

export interface ComposerText {
  tab: string;
  label: string;
  button: string;
  hint: string;
}

export interface Conversation {
  id: number;
  name: string;
  email: string;
  phone: string;
  interest: string;
  status: string;
  calcStatus: string;
  isTrashed: boolean;
  assignedUserId: number;
  animal: Animal | null;
  createdLabel: string;
  facts: Fact[];
  participants: string[];
  messages: Message[];
  composer: Record<ComposerMode, ComposerText>;
  pendingApproval: boolean;
}

export type ConversationAction =
  | "trash"
  | "restore"
  | "spam"
  | "unspam"
  | "block"
  | "unblock"
  | "approve-owner";

export interface Candidate {
  id: number;
  anfrageId: number;
  receivedLabel: string;
  sender: string;
  subject: string;
  excerpt: string;
  isSpamFolder: boolean;
}
