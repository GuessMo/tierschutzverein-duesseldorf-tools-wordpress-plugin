import { cn } from "@/lib/utils";
import { useLibraryStore } from "@/store/libraryStore";
import { DocumentPreview } from "./DocumentPreview";
import type { TemplateCardProps } from "./library.types";

export function TemplateCard({ preview }: TemplateCardProps) {
  const createFromTemplate = useLibraryStore((state) => state.createFromTemplate);

  return (
    <button
      type="button"
      onClick={() => void createFromTemplate(preview.id)}
      className={cn(
        "flex w-full flex-col gap-2 rounded-xl border bg-card p-2",
        "text-left text-card-foreground shadow-xs outline-none",
        "transition-colors hover:bg-surface-hover active:bg-accent",
        "focus-visible:ring-3 focus-visible:ring-ring/50",
      )}
    >
      <span className="grid h-52 place-items-center rounded-lg bg-muted">
        <DocumentPreview document={preview} maxWidth={170} maxHeight={190} />
      </span>
      <span className="px-1 pb-1 text-sm font-medium">{preview.name}</span>
    </button>
  );
}
