import { useEffect, useState } from "react";
import type { Route } from "@/types";

const PREFIX = "#tier/";
const NEW = "neu";

function readRoute(): Route {
  const { hash } = window.location;
  if (hash === STATS_HASH) return { view: "stats" };
  if (!hash.startsWith(PREFIX)) return { view: "list" };
  const rest = hash.slice(PREFIX.length);
  if (rest === NEW) return { view: "new" };
  const id = Number.parseInt(rest, 10);
  return Number.isNaN(id) ? { view: "list" } : { view: "edit", id };
}

export function animalHash(id: number): string {
  return `${PREFIX}${id}`;
}

export const NEW_ANIMAL_HASH = `${PREFIX}${NEW}`;

export const LIST_HASH = "#";

export const STATS_HASH = "#statistik";

export function useHashRoute(): Route {
  const [route, setRoute] = useState(readRoute);

  useEffect(() => {
    const onChange = () => setRoute(readRoute());
    window.addEventListener("hashchange", onChange);
    return () => window.removeEventListener("hashchange", onChange);
  }, []);

  return route;
}
