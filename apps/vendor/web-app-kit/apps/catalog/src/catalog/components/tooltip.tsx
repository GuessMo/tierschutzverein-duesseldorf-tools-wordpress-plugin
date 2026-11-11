import { IconInfoCircle } from "@tabler/icons-react";
import {
  Button,
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function TooltipDefault() {
  return (
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger asChild>
          <Button variant="outline" size="icon" aria-label="Hinweis zur Freigabe">
            <IconInfoCircle aria-hidden />
          </Button>
        </TooltipTrigger>
        <TooltipContent>Freigaben gelten für alle Entwürfe im Projekt.</TooltipContent>
      </Tooltip>
    </TooltipProvider>
  );
}

export const tooltipEntry: CatalogEntry = {
  slug: "tooltip",
  name: "Tooltip",
  group: "actions",
  status: "stable",
  description: "Zusatzinfo bei Hover und Fokus. Nie die einzige Beschriftung eines Bedienelements.",
  exports: ["Tooltip", "TooltipTrigger", "TooltipContent", "TooltipProvider"],
  variants: [
    {
      key: "default",
      title: "Am Icon-Button",
      description: "Mit Maus überfahren oder per Tab fokussieren; Escape schließt.",
      Preview: TooltipDefault,
    },
  ],
  states: [
    { name: "Geschlossen", description: "Standard, nur der Auslöser ist sichtbar." },
    { name: "Offen", description: "Bei Hover oder Tastaturfokus, ohne Verzögerung." },
  ],
  previewMinHeight: "8rem",
};
