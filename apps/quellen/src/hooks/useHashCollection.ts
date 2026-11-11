import { useEffect, useState } from "react";

const PREFIX = "#sammlung/";

function readHash(): number | null {
  if (!window.location.hash.startsWith(PREFIX)) return null;
  const id = Number.parseInt(window.location.hash.slice(PREFIX.length), 10);
  return Number.isNaN(id) ? null : id;
}

export function collectionHash(id: number): string {
  return `${PREFIX}${id}`;
}

export function useHashCollection(): number | null {
  const [collectionId, setCollectionId] = useState(readHash);

  useEffect(() => {
    const onChange = () => setCollectionId(readHash());
    window.addEventListener("hashchange", onChange);
    return () => window.removeEventListener("hashchange", onChange);
  }, []);

  return collectionId;
}
