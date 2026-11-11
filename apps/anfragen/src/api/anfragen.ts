import type {
  ActionResult,
  AnfragenContext,
  Candidate,
  ComposerMode,
  Conversation,
  ConversationAction,
  ListFilter,
  ListResponse,
  RelayedFilter,
  RelayedResponse,
  Settings,
} from "@/types";
import { send } from "./client";

const BASE = "anfragen";

function listQuery(filter: ListFilter): string {
  const params = new URLSearchParams({
    status: filter.status,
    search: filter.search,
    breed: String(filter.breed),
    animal: String(filter.animal),
    page: String(filter.page),
  });
  return params.toString();
}

function relayedQuery(filter: RelayedFilter): string {
  return new URLSearchParams({
    kind: filter.kind,
    search: filter.search,
    month: filter.month,
    page: String(filter.page),
  }).toString();
}

export const relayedApi = {
  list: (filter: RelayedFilter) =>
    send<RelayedResponse>("GET", `${BASE}/relayed?${relayedQuery(filter)}`),
};

export const anfragenApi = {
  list: (filter: ListFilter) => send<ListResponse>("GET", `${BASE}?${listQuery(filter)}`),
  context: () => send<AnfragenContext>("GET", `${BASE}/context`),
  saveSettings: (settings: Partial<Settings>) =>
    send<Settings>("PATCH", `${BASE}/settings`, settings),
  get: (id: number) => send<Conversation>("GET", `${BASE}/${id}`),
  remove: (id: number) => send<ActionResult>("DELETE", `${BASE}/${id}`),
  assign: (id: number, userId: number) =>
    send<ActionResult>("POST", `${BASE}/${id}/assign`, { userId }),
  run: (id: number, action: ConversationAction) =>
    send<ActionResult>("POST", `${BASE}/${id}/actions/${action}`),
};

export const messagesApi = {
  create: (id: number, mode: ComposerMode, body: string) =>
    send<ActionResult>("POST", `${BASE}/${id}/messages`, { mode, body }),
  update: (id: number, messageId: number, body: string) =>
    send<ActionResult>("PATCH", `${BASE}/${id}/messages/${messageId}`, { body }),
  remove: (id: number, messageId: number) =>
    send<ActionResult>("DELETE", `${BASE}/${id}/messages/${messageId}`),
  sendNow: (id: number, messageId: number) =>
    send<ActionResult>("POST", `${BASE}/${id}/messages/${messageId}/send`),
};

export const candidatesApi = {
  list: () => send<Candidate[]>("GET", `${BASE}/candidates`),
  decide: (id: number, decision: "approve" | "reject") =>
    send<ActionResult>("POST", `${BASE}/candidates/${id}/${decision}`),
};
