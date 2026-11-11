import { cn } from "@/lib/utils";
import { Artboard } from "@/components/artboard";
import { resolveDimensions } from "@/config/dimensions";
import type { DocumentPreviewProps } from "./library.types";

export function DocumentPreview({ document, maxWidth, maxHeight }: DocumentPreviewProps) {
  const { outerRatio } = resolveDimensions(document);
  const width = Math.min(maxWidth, maxHeight * outerRatio);
  const pageCount = document.pages.length;
  return (
    <div className="relative" aria-hidden="true" inert>
      <Artboard
        document={document}
        page={document.pages[0]}
        width={Math.round(width)}
        height={Math.round(width / outerRatio)}
        onBackgroundClick={() => undefined}
        isStatic
      />
      {pageCount > 1 && (
        <span
          className={cn(
            "absolute right-1 bottom-1 rounded-md bg-background/90 px-1.5",
            "py-0.5 text-[0.65rem] font-medium text-foreground shadow-xs",
          )}
        >
          {pageCount} Seiten
        </span>
      )}
    </div>
  );
}
