import { useCallback, useEffect, useState } from "react";

export type Area = "conversations" | "relayed";

const PARAM = "bereich";
const RELAYED = "halter";

function readArea(): Area {
  return new URLSearchParams(window.location.search).get(PARAM) === RELAYED
    ? "relayed"
    : "conversations";
}

export function useArea(): [Area, (area: Area) => void] {
  const [area, setArea] = useState<Area>(readArea);
  useEffect(() => {
    const onPopState = () => setArea(readArea());
    window.addEventListener("popstate", onPopState);
    return () => window.removeEventListener("popstate", onPopState);
  }, []);
  const change = useCallback((next: Area) => {
    const url = new URL(window.location.href);
    url.search = next === "relayed" ? `?${PARAM}=${RELAYED}` : "";
    window.history.pushState(null, "", url);
    setArea(next);
  }, []);
  return [area, change];
}
