import { useCallback, useState } from "react";
import { relayedApi } from "@/api";
import type { RelayedFilter } from "@/types";
import { useResource } from "./useResource";

const INITIAL: RelayedFilter = { kind: "", search: "", month: "", page: 1 };

export function useRelayed() {
  const [filter, setFilter] = useState<RelayedFilter>(INITIAL);
  const load = useCallback(() => relayedApi.list(filter), [filter]);
  const list = useResource(load);
  const update = useCallback((patch: Partial<Omit<RelayedFilter, "page">>) => {
    setFilter((current) => ({ ...current, ...patch, page: 1 }));
  }, []);
  const setPage = useCallback((page: number) => {
    setFilter((current) => ({ ...current, page }));
  }, []);
  return { filter, update, setPage, list };
}

export type RelayedState = ReturnType<typeof useRelayed>;
