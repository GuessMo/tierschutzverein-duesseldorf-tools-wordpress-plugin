import { createPortal } from "react-dom";
import { Artboard } from "@/components/artboard";
import { MARK_AREA_MM, resolveDimensions } from "@/config/dimensions";
import { sheetSize } from "@/services/exportPdf";
import { useEditorStore } from "@/store/editorStore";
import { usePrintStore } from "@/store/printStore";
import { CropMarks } from "./CropMarks";

export function PrintSheets() {
  const isPrinting = usePrintStore((state) => state.isPrinting);
  const document = useEditorStore((state) => state.document);
  if (!isPrinting || !document) return null;
  const dimensions = resolveDimensions(document);
  const sheet = sheetSize(dimensions);
  const outerWidth = dimensions.widthMm + 2 * dimensions.bleedMm;
  const outerHeight = dimensions.heightMm + 2 * dimensions.bleedMm;

  return createPortal(
    <div className="print-sheets">
      {document.pages.map((page) => (
        <div
          key={page.id}
          className="print-sheet"
          style={{ width: `${sheet.width}mm`, height: `${sheet.height}mm` }}
        >
          <div
            className="print-sheet__board"
            style={{ left: `${MARK_AREA_MM}mm`, top: `${MARK_AREA_MM}mm` }}
          >
            <Artboard
              document={document}
              page={page}
              width={`${outerWidth}mm`}
              height={`${outerHeight}mm`}
              onBackgroundClick={() => undefined}
              isStatic
            />
          </div>
          <CropMarks dimensions={dimensions} />
        </div>
      ))}
    </div>,
    window.document.body,
  );
}
