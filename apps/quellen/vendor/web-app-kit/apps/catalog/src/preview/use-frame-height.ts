import { useEffect, useRef, useState } from "react";

function observeBody(frame: HTMLIFrameElement, onHeight: (height: number) => void) {
  const body = frame.contentDocument?.body;
  if (!body) return null;
  const observer = new ResizeObserver(() => onHeight(body.scrollHeight));
  observer.observe(body);
  onHeight(body.scrollHeight);
  return observer;
}

export function useFrameHeight(src: string) {
  const frameRef = useRef<HTMLIFrameElement>(null);
  const [height, setHeight] = useState<number | null>(null);

  useEffect(() => {
    const frame = frameRef.current;
    if (!frame) return;
    let observer: ResizeObserver | null = null;
    const onLoad = () => {
      observer?.disconnect();
      observer = observeBody(frame, setHeight);
    };
    frame.addEventListener("load", onLoad);
    return () => {
      frame.removeEventListener("load", onLoad);
      observer?.disconnect();
    };
  }, [src]);

  return { frameRef, height };
}
