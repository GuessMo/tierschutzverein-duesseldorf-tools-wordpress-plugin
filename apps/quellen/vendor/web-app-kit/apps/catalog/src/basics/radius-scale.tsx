import type { ScaleItem } from "./basics.types";
import { ScaleTile } from "./scale-tile";

const RADII: ScaleItem[] = [
  { name: "rounded-sm", className: "rounded-sm bg-muted" },
  { name: "rounded-md", className: "rounded-md bg-muted" },
  { name: "rounded-lg", className: "rounded-lg bg-muted" },
  { name: "rounded-xl", className: "rounded-xl bg-muted" },
  { name: "rounded-2xl", className: "rounded-2xl bg-muted" },
  { name: "rounded-3xl", className: "rounded-3xl bg-muted" },
  { name: "rounded-4xl", className: "rounded-4xl bg-muted" },
];

export function RadiusScale() {
  return (
    <div className="grid grid-cols-[repeat(auto-fill,minmax(8rem,1fr))] gap-3">
      {RADII.map((item) => (
        <ScaleTile key={item.name} {...item} property="borderTopLeftRadius" />
      ))}
    </div>
  );
}
