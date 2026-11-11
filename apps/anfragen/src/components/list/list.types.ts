import type { ConversationListState } from "@/hooks";
import type { CountKey, House, ListItem, ListStatus } from "@/types";

export interface ConversationSidebarProps {
  state: ConversationListState;
  selectedId: number | null;
  onSelect: (id: number) => void;
}

export interface StatusFilterProps {
  value: ListStatus;
  counts: Record<CountKey, number> | null;
  onChange: (status: ListStatus) => void;
}

export interface HouseFilterProps {
  value: number;
  houses: House[];
  onChange: (breed: number) => void;
}

export interface SearchFieldProps {
  value: string;
  onChange: (search: string) => void;
}

export interface ConversationItemProps {
  item: ListItem;
  isSelected: boolean;
  onSelect: () => void;
}

export interface ListPagerProps {
  page: number;
  pages: number;
  onPage: (page: number) => void;
}
