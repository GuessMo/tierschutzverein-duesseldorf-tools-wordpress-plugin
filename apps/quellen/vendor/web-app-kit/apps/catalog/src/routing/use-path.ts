import { useSyncExternalStore } from "react";
import { readPath, subscribeToPath } from "./navigation";

export function usePath(): string {
  return useSyncExternalStore(subscribeToPath, readPath, () => "/");
}
