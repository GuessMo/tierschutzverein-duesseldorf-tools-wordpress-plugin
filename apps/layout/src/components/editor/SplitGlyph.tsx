import { findSplit } from "@/config/splits";
import type { SplitId } from "@/types/document";

export function SplitGlyph({ split }: { split: SplitId }) {
  return (
    <span className="flex h-4 w-7 shrink-0 gap-0.5" aria-hidden="true">
      {findSplit(split).columns.map((weight, index) => (
        <span
          key={index}
          className="basis-0 rounded-[2px] bg-muted-foreground/40"
          style={{ flexGrow: weight }}
        />
      ))}
    </span>
  );
}
