import { useEffect, useState } from "react";

export function useCountdown(seconds: number, onDone: () => void): number {
  const [remaining, setRemaining] = useState(seconds);

  useEffect(() => {
    if (remaining <= 0) {
      const timer = window.setTimeout(onDone, 2000);
      return () => window.clearTimeout(timer);
    }
    const timer = window.setTimeout(() => setRemaining((value) => value - 1), 1000);
    return () => window.clearTimeout(timer);
  }, [remaining, onDone]);

  return remaining;
}
