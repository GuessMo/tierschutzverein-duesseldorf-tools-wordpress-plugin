import { forwardRef, type CSSProperties } from "react";
import { resolveDimensions } from "@/config/dimensions";
import { findTone } from "@/config/tones";
import type { ArtboardProps } from "./artboard.types";
import { SectionView } from "./SectionView";

export const Artboard = forwardRef<HTMLDivElement, ArtboardProps>(function Artboard(
  { document, page, width, height, onBackgroundClick, isStatic },
  ref,
) {
  const { bleedPercent } = resolveDimensions(document);
  const style = {
    width,
    height,
    background: findTone(document.tone ?? "white").background,
    "--bleed": bleedPercent,
  } as CSSProperties;

  return (
    <div
      ref={ref}
      className={isStatic ? "artboard is-static" : "artboard"}
      data-bleed={bleedPercent > 0 || undefined}
      style={style}
      onPointerUp={(event) => event.target === event.currentTarget && onBackgroundClick()}
    >
      {page.sections.map((section) => (
        <SectionView key={section.id} section={section} />
      ))}
      {bleedPercent > 0 && <span className="artboard__trim" aria-hidden="true" />}
    </div>
  );
});
