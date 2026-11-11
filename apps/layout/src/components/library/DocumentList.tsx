import { Button } from "@web-app-kit/frontend/ui";
import { cn } from "@/lib/utils";
import { IconTrash } from "@tabler/icons-react";
import { findFormat } from "@/config/formats";
import { useLibraryStore } from "@/store/libraryStore";
import { DocumentPreview } from "./DocumentPreview";

const DATE_FORMAT = new Intl.DateTimeFormat("de-DE", { dateStyle: "medium", timeStyle: "short" });

export function DocumentList() {
  const documents = useLibraryStore((state) => state.documents);
  const { open, remove } = useLibraryStore.getState();

  return (
    <section className="flex flex-col gap-4" aria-labelledby="documents-title">
      <h2 id="documents-title" className="text-lg font-semibold">
        Deine Dokumente
      </h2>
      {documents.length === 0 && (
        <p className="text-sm text-muted-foreground">
          Noch nichts gestaltet. Starte unten mit einer Vorlage.
        </p>
      )}
      <ul className="grid grid-cols-[repeat(auto-fill,minmax(12rem,1fr))] gap-3">
        {documents.map((document) => (
          <li key={document.id} className="relative">
            <button
              type="button"
              onClick={() => open(document.id)}
              className={cn(
                "flex w-full flex-col gap-2 rounded-xl border bg-card p-2",
                "text-left text-card-foreground shadow-xs outline-none",
                "transition-colors hover:bg-surface-hover active:bg-accent",
                "focus-visible:ring-3 focus-visible:ring-ring/50",
              )}
            >
              <span className="grid h-52 place-items-center rounded-lg bg-muted">
                <DocumentPreview document={document} maxWidth={170} maxHeight={190} />
              </span>
              <span className="flex flex-col gap-0.5 px-1 pb-1">
                <span className="truncate text-sm font-medium">{document.name}</span>
                <span className="text-xs text-muted-foreground">
                  {findFormat(document.formatId).label} ·{" "}
                  {DATE_FORMAT.format(new Date(document.updatedAt))}
                </span>
              </span>
            </button>
            <Button
              variant="outline"
              size="icon-sm"
              className="absolute top-3 right-3"
              aria-label={`${document.name} löschen`}
              onClick={() =>
                window.confirm(`„${document.name}“ löschen?`) && void remove(document.id)
              }
            >
              <IconTrash />
            </Button>
          </li>
        ))}
      </ul>
    </section>
  );
}
