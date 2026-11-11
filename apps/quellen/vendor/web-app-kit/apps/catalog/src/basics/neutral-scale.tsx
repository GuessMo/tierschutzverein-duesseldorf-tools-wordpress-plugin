import { useLayoutEffect, useState } from "react";
import type { ScaleItem } from "./basics.types";
import { ScaleTile } from "./scale-tile";

const NEUTRALS: ScaleItem[] = [
  { name: "background", className: "rounded-md bg-background" },
  { name: "card", className: "rounded-md bg-card" },
  { name: "popover", className: "rounded-md bg-popover" },
  { name: "muted", className: "rounded-md bg-muted" },
  { name: "accent", className: "rounded-md bg-accent" },
  { name: "border", className: "rounded-md bg-border" },
  { name: "input", className: "rounded-md bg-input" },
  { name: "muted-foreground", className: "rounded-md bg-muted-foreground" },
  { name: "foreground", className: "rounded-md bg-foreground" },
];

function useNeutralTint() {
  const [tint, setTint] = useState({ hue: "", chroma: "" });
  useLayoutEffect(() => {
    const style = getComputedStyle(document.documentElement);
    setTint({
      hue: style.getPropertyValue("--neutral-hue").trim(),
      chroma: style.getPropertyValue("--neutral-chroma").trim(),
    });
  }, []);
  return tint;
}

export function NeutralScale() {
  const tint = useNeutralTint();
  return (
    <div className="flex flex-col gap-4">
      <p className="text-sm">
        Farbstich: <code className="font-mono">--neutral-hue: {tint.hue}</code>,{" "}
        <code className="font-mono">--neutral-chroma: {tint.chroma}</code>
      </p>
      <div className="grid grid-cols-[repeat(auto-fill,minmax(8rem,1fr))] gap-3">
        {NEUTRALS.map((item) => (
          <ScaleTile key={item.name} {...item} property="backgroundColor" />
        ))}
      </div>
    </div>
  );
}
