import { useCallback, useState } from "react";
import { anfragenApi } from "@/api";
import type { SidebarPosition } from "@/types";

export function useSidebarPosition(initial: SidebarPosition): [SidebarPosition, () => void] {
  const [position, setPosition] = useState(initial);

  const toggle = useCallback(() => {
    const next = position === "left" ? "right" : "left";
    setPosition(next);
    void anfragenApi.saveSettings({ sidebarPos: next }).catch(() => undefined);
  }, [position]);

  return [position, toggle];
}
