import { IconAlertTriangle } from "@tabler/icons-react";
import { Notice } from "./Notice";
import { formatRatio } from "@/services/contrast";
import { useContrastStore } from "@/store/contrastStore";

export function ContrastAlert({ elementId }: { elementId: string }) {
  const issue = useContrastStore((state) =>
    state.issues.find((entry) => entry.elementId === elementId),
  );
  if (!issue) return null;
  const hint = issue.isOnPhoto
    ? "Wähle eine hellere oder dunklere Schriftfarbe, schalte „Abdunkeln“ am " +
      "Bereich ein oder nimm ein ruhigeres Foto."
    : "Wähle eine andere Schrift- oder Hintergrundfarbe oder eine größere Schrift.";
  return (
    <Notice
      icon={<IconAlertTriangle aria-hidden />}
      title={`Kontrast zu gering: ${formatRatio(issue.ratio)}`}
    >
      Nötig sind mindestens {formatRatio(issue.required)} (WCAG 2.2 AA). {hint}
    </Notice>
  );
}
