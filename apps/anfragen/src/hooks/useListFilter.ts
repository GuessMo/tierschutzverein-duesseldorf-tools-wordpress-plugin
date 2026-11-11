import { useCallback, useState } from "react";
import { anfragenApi } from "@/api";
import type { ListFilter, Settings } from "@/types";

export function useListFilter(settings: Settings) {
  const [filter, setFilter] = useState<ListFilter>({
    status: settings.status,
    search: "",
    breed: settings.breed,
    page: 1,
  });

  const update = useCallback((patch: Partial<Omit<ListFilter, "page">>) => {
    setFilter((current) => ({ ...current, ...patch, page: 1 }));
    const { status, breed } = patch;
    if (status === undefined && breed === undefined) return;
    void anfragenApi.saveSettings({ status, breed }).catch(() => undefined);
  }, []);

  const setPage = useCallback((page: number) => {
    setFilter((current) => ({ ...current, page }));
  }, []);

  return { filter, update, setPage };
}
