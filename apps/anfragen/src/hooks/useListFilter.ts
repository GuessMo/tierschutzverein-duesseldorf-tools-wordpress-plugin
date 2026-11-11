import { useCallback, useState } from "react";
import { anfragenApi } from "@/api";
import type { ListFilter, Settings } from "@/types";
import { readAnimalParam, writeAnimalParam } from "./animalParam";

export function useListFilter(settings: Settings) {
  const [filter, setFilter] = useState<ListFilter>({
    status: settings.status,
    search: "",
    breed: settings.breed,
    animal: readAnimalParam(),
    page: 1,
  });

  const update = useCallback((patch: Partial<Omit<ListFilter, "page">>) => {
    setFilter((current) => ({ ...current, ...patch, page: 1 }));
    const { status, breed } = patch;
    if (status === undefined && breed === undefined) return;
    void anfragenApi.saveSettings({ status, breed }).catch(() => undefined);
  }, []);

  const clearAnimal = useCallback(() => {
    writeAnimalParam(0);
    setFilter((current) => ({ ...current, animal: 0, page: 1 }));
  }, []);

  const setPage = useCallback((page: number) => {
    setFilter((current) => ({ ...current, page }));
  }, []);

  return { filter, update, setPage, clearAnimal };
}
