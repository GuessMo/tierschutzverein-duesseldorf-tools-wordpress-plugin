import { useCallback } from "react";
import { anfragenApi } from "@/api";
import type { Settings } from "@/types";
import { useListFilter } from "./useListFilter";
import { useResource } from "./useResource";

export function useConversationList(settings: Settings) {
  const { filter, update, setPage } = useListFilter(settings);
  const load = useCallback(() => anfragenApi.list(filter), [filter]);
  const list = useResource(load);
  return { filter, update, setPage, list };
}

export type ConversationListState = ReturnType<typeof useConversationList>;
