import type { CSSProperties } from "react";
import { MARK_AREA_MM } from "@/config/dimensions";
import type { PageDimensions } from "@/types/catalog";

const MARK_GAP_MM = 1;

function line(style: CSSProperties, key: string) {
  return <span key={key} className="crop-mark" style={style} />;
}

export function CropMarks({ dimensions }: { dimensions: PageDimensions }) {
  const offset = MARK_AREA_MM + dimensions.bleedMm;
  const length = MARK_AREA_MM - MARK_GAP_MM;
  const xs = [offset, offset + dimensions.widthMm];
  const ys = [offset, offset + dimensions.heightMm];
  const farX = offset * 2 + dimensions.widthMm - length;
  const farY = offset * 2 + dimensions.heightMm - length;
  return (
    <>
      {ys.flatMap((y) => [
        line({ top: `${y}mm`, left: 0, width: `${length}mm`, height: 0 }, `h-l-${y}`),
        line({ top: `${y}mm`, left: `${farX}mm`, width: `${length}mm`, height: 0 }, `h-r-${y}`),
      ])}
      {xs.flatMap((x) => [
        line({ left: `${x}mm`, top: 0, height: `${length}mm`, width: 0 }, `v-t-${x}`),
        line({ left: `${x}mm`, top: `${farY}mm`, height: `${length}mm`, width: 0 }, `v-b-${x}`),
      ])}
    </>
  );
}
