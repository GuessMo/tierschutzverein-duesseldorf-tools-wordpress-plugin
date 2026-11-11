import { useEffect } from "react";

export function useAutoRefresh(seconds: number, refresh: () => void) {
  useEffect(() => {
    if (seconds <= 0) return;
    const timer = window.setInterval(() => {
      if (document.visibilityState === "visible") refresh();
    }, seconds * 1000);
    return () => window.clearInterval(timer);
  }, [seconds, refresh]);
}
