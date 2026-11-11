import { IconX } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import { AnimalThumb, FormSection } from "@/components/common";
import { animalHash } from "@/hooks";
import type { SectionProps } from "../editor.types";

export function CompanionsSection({ draft, onChange }: SectionProps) {
  if (draft.companions.length === 0) return null;
  const remove = (id: number) =>
    onChange({ companions: draft.companions.filter((companion) => companion.id !== id) });
  return (
    <FormSection
      id="companions"
      title="Partnertiere"
      description="Diese Tiere werden gemeinsam vermittelt. Entfernen wirkt nach dem Speichern."
    >
      <ul className="flex flex-col divide-y border-y">
        {draft.companions.map((companion) => (
          <li key={companion.id} className="flex items-center gap-3 py-2">
            <AnimalThumb src={companion.thumb} />
            <div className="flex min-w-0 flex-1 flex-col">
              <Button asChild variant="link" className="h-auto justify-start px-0 text-foreground">
                <a href={animalHash(companion.id)}>{companion.title}</a>
              </Button>
              <span className="truncate text-xs text-muted-foreground">
                {[companion.internal_id, companion.breed].filter(Boolean).join(" · ")}
              </span>
            </div>
            <Button
              type="button"
              variant="ghost"
              size="icon-sm"
              aria-label={`${companion.title} als Partnertier entfernen`}
              onClick={() => remove(companion.id)}
            >
              <IconX />
            </Button>
          </li>
        ))}
      </ul>
    </FormSection>
  );
}
