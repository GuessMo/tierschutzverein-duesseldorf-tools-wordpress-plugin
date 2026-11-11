import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import { BLOCKS_HASH, ISSUES_HASH, SUBSCRIBERS_HASH } from "@/hooks";
import type { NewsletterRoute } from "@/types";

const HASHES = { issues: ISSUES_HASH, subscribers: SUBSCRIBERS_HASH, blocks: BLOCKS_HASH };

type Area = keyof typeof HASHES;

function areaOf(route: NewsletterRoute): Area {
  if (route.view === "subscribers" || route.view === "blocks") return route.view;
  return "issues";
}

export function AreaNav({ route }: { route: NewsletterRoute }) {
  return (
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label="Bereich"
      value={areaOf(route)}
      onValueChange={(next) => {
        if (next) window.location.hash = HASHES[next as Area];
      }}
    >
      <ToggleGroupItem value="issues">Ausgaben</ToggleGroupItem>
      <ToggleGroupItem value="subscribers">Abonnenten</ToggleGroupItem>
      <ToggleGroupItem value="blocks" aria-label="Dynamische Blöcke">
        <span className="sm:hidden">Blöcke</span>
        <span className="hidden sm:inline">Dynamische Blöcke</span>
      </ToggleGroupItem>
    </ToggleGroup>
  );
}
