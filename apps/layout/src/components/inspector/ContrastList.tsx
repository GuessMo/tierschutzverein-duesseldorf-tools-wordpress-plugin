import { cn } from "@/lib/utils";
import { IconAlertTriangle, IconCircleCheck } from "@tabler/icons-react";
import { formatRatio } from "@/services/contrast";
import { useContrastStore } from "@/store/contrastStore";
import { useEditorStore } from "@/store/editorStore";

export function ContrastList() {
  const issues = useContrastStore((state) => state.issues);
  const select = useEditorStore((state) => state.select);

  if (issues.length === 0) {
    return (
      <p className="flex items-center gap-2 text-sm text-muted-foreground">
        <IconCircleCheck className="size-4" />
        Alle Texte erfüllen WCAG 2.2 AA.
      </p>
    );
  }
  return (
    <div className="flex flex-col gap-2">
      <p className="flex items-center gap-2 text-sm font-medium text-destructive">
        <IconAlertTriangle className="size-4" />
        {issues.length === 1
          ? "1 Text mit zu wenig Kontrast"
          : `${issues.length} Texte mit zu wenig Kontrast`}
      </p>
      <ul className="flex flex-col gap-1">
        {issues.map((issue) => (
          <li key={issue.elementId}>
            <button
              type="button"
              className={cn(
                "flex w-full items-center justify-between gap-2 rounded-md",
                "px-2 py-1.5 text-left text-sm hover:bg-surface-hover",
                "focus-visible:ring-3 focus-visible:ring-ring/50",
                "focus-visible:outline-none active:bg-accent",
              )}
              onClick={() =>
                select({
                  type: "element",
                  sectionId: issue.sectionId,
                  cellId: issue.cellId,
                  elementId: issue.elementId,
                })
              }
            >
              <span className="truncate">{issue.label}</span>
              <span className="shrink-0 tabular-nums text-muted-foreground">
                {formatRatio(issue.ratio)} / {formatRatio(issue.required)}
              </span>
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
}
