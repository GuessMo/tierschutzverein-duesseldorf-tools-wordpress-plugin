import { Button } from "@web-app-kit/frontend/ui";
import { IconAlertTriangle, IconCircleCheck } from "@tabler/icons-react";
import { useContrastStore } from "@/store/contrastStore";
import { useEditorStore } from "@/store/editorStore";

export function ContrastStatus() {
  const issues = useContrastStore((state) => state.issues);
  const select = useEditorStore((state) => state.select);
  const first = issues[0];

  if (!first) {
    return (
      <span className="flex items-center gap-1.5 px-2 text-sm text-muted-foreground">
        <IconCircleCheck className="size-4" />
        Kontrast ok
      </span>
    );
  }
  return (
    <Button
      variant="destructive"
      onClick={() =>
        select({
          type: "element",
          sectionId: first.sectionId,
          cellId: first.cellId,
          elementId: first.elementId,
        })
      }
    >
      <IconAlertTriangle data-icon="inline-start" />
      {issues.length === 1 ? "1 Kontrastproblem" : `${issues.length} Kontrastprobleme`}
    </Button>
  );
}
