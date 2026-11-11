import { useCallback, useEffect, useState } from "react";

const PARAM = "view";

function readSelection(): number | null {
  const value = Number(new URLSearchParams(window.location.search).get(PARAM));
  return Number.isInteger(value) && value > 0 ? value : null;
}

function writeSelection(id: number | null) {
  const url = new URL(window.location.href);
  if (id === null) url.searchParams.delete(PARAM);
  else url.searchParams.set(PARAM, String(id));
  window.history.pushState(null, "", url);
}

export function useSelection(): [number | null, (id: number | null) => void] {
  const [selectedId, setSelectedId] = useState(readSelection);

  useEffect(() => {
    const onPopState = () => setSelectedId(readSelection());
    window.addEventListener("popstate", onPopState);
    return () => window.removeEventListener("popstate", onPopState);
  }, []);

  const select = useCallback((id: number | null) => {
    writeSelection(id);
    setSelectedId(id);
  }, []);

  return [selectedId, select];
}
