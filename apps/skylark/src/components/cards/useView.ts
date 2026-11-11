import { useStoredState } from "@/hooks";
import type { ViewOption } from "./ViewToggle";

export function useView(id: string, options: ViewOption[]) {
  const first = options[0]?.value ?? "";
  const [stored, setView] = useStoredState(`view:${id}`, first);
  const view = options.some((option) => option.value === stored) ? stored : first;
  return [view, setView] as const;
}
