import { Button } from "@web-app-kit/frontend/ui";
import { IconArrowDown, IconArrowUp, IconCopy, IconPlus, IconTrash } from "@tabler/icons-react";
import { PanelSection } from "@/components/controls";
import { cn } from "@/lib/utils";
import { addPage, clonePage, createPage, movePage, removePage } from "@/services/pageOperations";
import { useEditorStore } from "@/store/editorStore";

export function PageList() {
  const pages = useEditorStore((state) => state.document?.pages ?? []);
  const activePageId = useEditorStore((state) => state.activePageId);
  const { apply, showPage } = useEditorStore.getState();

  const insert = (page = createPage()) => {
    apply((document) => addPage(document, page, activePageId));
    showPage(page.id);
  };

  return (
    <PanelSection title="Seiten">
      <ol className="flex flex-col gap-1">
        {pages.map((page, index) => (
          <li
            key={page.id}
            className={cn(
              "group flex items-center rounded-md hover:bg-surface-hover",
              page.id === activePageId && "bg-surface-selected",
            )}
          >
            <button
              type="button"
              aria-current={page.id === activePageId ? "page" : undefined}
              className={cn(
                "flex min-h-8 flex-1 items-center rounded-md px-2 text-left",
                "text-sm outline-none focus-visible:ring-3",
                "focus-visible:ring-ring/50 hover:underline active:bg-surface-selected",
              )}
              onClick={() => showPage(page.id)}
            >
              Seite {index + 1}
            </button>
            <span className="flex opacity-60 group-hover:opacity-100 focus-within:opacity-100">
              <Button
                variant="ghost"
                size="icon-xs"
                aria-label={`Seite ${index + 1} nach oben`}
                disabled={index === 0}
                onClick={() => apply((document) => movePage(document, page.id, -1))}
              >
                <IconArrowUp />
              </Button>
              <Button
                variant="ghost"
                size="icon-xs"
                aria-label={`Seite ${index + 1} nach unten`}
                disabled={index === pages.length - 1}
                onClick={() => apply((document) => movePage(document, page.id, 1))}
              >
                <IconArrowDown />
              </Button>
              <Button
                variant="ghost"
                size="icon-xs"
                aria-label={`Seite ${index + 1} duplizieren`}
                onClick={() => insert(clonePage(page))}
              >
                <IconCopy />
              </Button>
              <Button
                variant="ghost"
                size="icon-xs"
                aria-label={`Seite ${index + 1} löschen`}
                disabled={pages.length === 1}
                onClick={() => apply((document) => removePage(document, page.id))}
              >
                <IconTrash />
              </Button>
            </span>
          </li>
        ))}
      </ol>
      <Button variant="outline" size="sm" onClick={() => insert()}>
        <IconPlus data-icon="inline-start" />
        Seite hinzufügen
      </Button>
    </PanelSection>
  );
}
