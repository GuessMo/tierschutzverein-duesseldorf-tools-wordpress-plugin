export type ListStatus = "" | "open" | "answered" | "mine" | "spam" | "blocked" | "trash";

export type CountKey = "all" | Exclude<ListStatus, "">;

export type WaitLevel = "" | "warn" | "alert";

export type SidebarPosition = "left" | "right";

export interface ListFilter {
  status: ListStatus;
  search: string;
  breed: number;
  animal: number;
  page: number;
}

export interface ListItem {
  id: number;
  name: string;
  animal: string;
  status: string;
  calcStatus: string;
  isOpen: boolean;
  waitLabel: string;
  waitLevel: WaitLevel;
  waitTitle: string;
  activityDay: string;
}

export interface House {
  id: number;
  name: string;
  icon: string;
}

export interface ListResponse {
  items: ListItem[];
  total: number;
  pages: number;
  page: number;
  counts: Record<CountKey, number>;
  houses: House[];
  animal: string;
}

export interface Settings {
  sidebarPos: SidebarPosition;
  status: ListStatus;
  breed: number;
}

export interface Assignee {
  id: number;
  label: string;
}

export interface AnfragenContext {
  settings: Settings;
  assignees: Assignee[];
  canManageCandidates: boolean;
  openCount: number;
  oldestOpenId: number;
}

export interface ActionResult {
  message: string;
}
