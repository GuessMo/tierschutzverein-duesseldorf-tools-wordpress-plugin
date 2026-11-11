import { useEffect, useState } from "react";

export interface CountryShape {
  id: string;
  d: string;
}

const cache = new Map<string, Promise<CountryShape[]>>();

async function fetchShapes(url: string): Promise<CountryShape[]> {
  const response = await fetch(url, { credentials: "same-origin" });
  if (!response.ok) throw new Error("Die Weltkarte konnte nicht geladen werden.");
  const doc = new DOMParser().parseFromString(await response.text(), "image/svg+xml");
  return Array.from(doc.querySelectorAll("path[id]")).map((path) => ({
    id: path.id.toUpperCase(),
    d: path.getAttribute("d") ?? "",
  }));
}

export function useWorldShapes(url: string) {
  const [shapes, setShapes] = useState<CountryShape[] | null>(null);
  const [failed, setFailed] = useState(false);
  useEffect(() => {
    let isCurrent = true;
    const request = cache.get(url) ?? fetchShapes(url);
    cache.set(url, request);
    request
      .then((result) => isCurrent && setShapes(result))
      .catch(() => isCurrent && setFailed(true));
    return () => {
      isCurrent = false;
    };
  }, [url]);
  return { shapes, failed };
}
