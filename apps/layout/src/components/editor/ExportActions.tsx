import { Button } from "@web-app-kit/frontend/ui";
import { IconDownload, IconPrinter } from "@tabler/icons-react";
import { useState } from "react";
import { resolveDimensions } from "@/config/dimensions";
import { exportPdf, supportsPdf } from "@/services/exportPdf";
import { exportPng, toFileName } from "@/services/exportPng";
import { useEditorStore } from "@/store/editorStore";
import type { TopBarProps } from "./editor.types";

export function ExportActions({ artboardRef }: TopBarProps) {
  const document = useEditorStore((state) => state.document);
  const select = useEditorStore((state) => state.select);
  const activePageId = useEditorStore((state) => state.activePageId);
  const [isExporting, setIsExporting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  if (!document) return null;
  const dimensions = resolveDimensions(document);
  const canPrint = supportsPdf(dimensions);
  const pageIndex = document.pages.findIndex((page) => page.id === activePageId);
  const pageSuffix = document.pages.length > 1 ? ` Seite ${pageIndex + 1}` : "";

  const handlePng = async () => {
    if (!artboardRef.current) return;
    setIsExporting(true);
    setErrorMessage(null);
    try {
      await exportPng(
        artboardRef.current,
        dimensions,
        toFileName(document.name + pageSuffix, "png"),
      );
    } catch {
      setErrorMessage("PNG-Export fehlgeschlagen. Bitte erneut versuchen.");
    } finally {
      setIsExporting(false);
    }
  };

  const handlePdf = () => {
    select({ type: "none" });
    window.setTimeout(() => void exportPdf(dimensions, document.name), 50);
  };

  return (
    <div className="flex items-center gap-2">
      {errorMessage && (
        <span className="text-sm text-destructive" role="alert">
          {errorMessage}
        </span>
      )}
      <Button variant="outline" onClick={handlePng} disabled={isExporting}>
        <IconDownload data-icon="inline-start" />
        {isExporting
          ? "Exportiere …"
          : `PNG${pageSuffix} ${dimensions.widthPx} × ${dimensions.heightPx}`}
      </Button>
      <Button
        onClick={handlePdf}
        disabled={!canPrint}
        title={
          canPrint
            ? "Alle Seiten mit 3 mm Anschnitt und Schnittmarken. Im Druckdialog „Als " +
              "PDF sichern“ wählen."
            : "Nur für Druckformate"
        }
      >
        <IconPrinter data-icon="inline-start" />
        PDF
      </Button>
    </div>
  );
}
