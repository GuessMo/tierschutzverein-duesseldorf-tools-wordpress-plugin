import { cn } from "@/lib/utils";
import { useMemo } from "react";
import { FORMATS } from "@/config/formats";
import { TEMPLATES } from "@/config/templates";
import { documentFromTemplate } from "@/services/templateDocument";
import type { LayoutDocument } from "@/types/document";
import { TemplateCard } from "./TemplateCard";

function usePreviewDocuments(): LayoutDocument[] {
  return useMemo(
    () => TEMPLATES.map((template) => documentFromTemplate(template, template.id)),
    [],
  );
}

function useFormatGroups() {
  const previews = usePreviewDocuments();
  const groups = FORMATS.map((format) => ({
    id: format.id,
    label: format.label,
    items: previews.filter((preview) => preview.formatId === format.id && preview.id !== "blank"),
  })).filter((group) => group.items.length > 0);
  return [
    ...groups,
    {
      id: "blank",
      label: "Ohne Vorlage",
      items: previews.filter((preview) => preview.id === "blank"),
    },
  ];
}

export function TemplateGallery() {
  const groups = useFormatGroups();

  return (
    <section className="flex flex-col gap-4" aria-labelledby="templates-title">
      <h2 id="templates-title" className="text-lg font-semibold">
        Neu aus Vorlage
      </h2>
      <div
        className={cn(
          "grid",
          "grid-cols-[repeat(auto-fill,minmax(min(100%,26rem),1fr))]",
          "gap-x-4 gap-y-6",
        )}
      >
        {groups.map((group) => (
          <section key={group.id} className="flex flex-col gap-2" aria-label={group.label}>
            <h3 className="text-sm font-medium text-muted-foreground">{group.label}</h3>
            <ul className="grid grid-cols-2 gap-3">
              {group.items.map((preview) => (
                <li key={preview.id}>
                  <TemplateCard preview={preview} />
                </li>
              ))}
            </ul>
          </section>
        ))}
      </div>
    </section>
  );
}
