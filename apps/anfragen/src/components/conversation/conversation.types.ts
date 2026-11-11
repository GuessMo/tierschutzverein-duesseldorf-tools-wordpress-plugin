import type {
  Animal,
  Assignee,
  AskConfirmation,
  Conversation,
  ConversationAction,
  Message,
} from "@/types";

export type MenuAction = ConversationAction | "delete";

export interface ConversationViewProps {
  id: number;
  assignees: Assignee[];
  onChanged: () => Promise<void>;
}

export interface ConversationHeaderProps {
  conversation: Conversation;
  assignees: Assignee[];
  onAction: (action: MenuAction) => void;
  onAssign: (userId: number) => void;
}

export interface ActionsMenuProps {
  conversation: Conversation;
  onAction: (action: MenuAction) => void;
}

export interface AssigneeSelectProps {
  value: number;
  assignees: Assignee[];
  onChange: (userId: number) => void;
}

export interface AnimalCardProps {
  animal: Animal;
}

export interface MessageBubbleProps {
  conversationId: number;
  message: Message;
  ask: AskConfirmation;
  onChanged: () => Promise<void>;
}

export interface ComposerProps {
  conversation: Conversation;
  onSent: () => Promise<void>;
}
