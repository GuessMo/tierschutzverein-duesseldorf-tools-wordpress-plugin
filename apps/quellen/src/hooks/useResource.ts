import { useCallback, useEffect, useState } from "react";
import { describeError } from "@web-app-kit/frontend";
import type { Resource } from "@/types";

type ResourceState<T> = Omit<Resource<T>, "reload">;

const LOAD_ERROR = "Die Daten konnten nicht geladen werden.";

export function useResource<T>(load: () => Promise<T>): Resource<T> {
  const [state, setState] = useState<ResourceState<T>>({
    data: null,
    error: null,
    isLoading: true,
  });

  const reload = useCallback(async () => {
    setState((current) => ({ ...current, isLoading: true, error: null }));
    try {
      const data = await load();
      setState({ data, error: null, isLoading: false });
    } catch (error) {
      setState((current) => ({
        ...current,
        error: describeError(error, LOAD_ERROR),
        isLoading: false,
      }));
    }
  }, [load]);

  useEffect(() => {
    void reload();
  }, [reload]);

  return { ...state, reload };
}
