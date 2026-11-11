import { IconPlus } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";

export function EntriesHeader({ onCreate }: { onCreate: () => void }) {
  return (
    <div className="flex flex-wrap items-center justify-between gap-3">
      <h2 className="text-lg font-semibold">Quellen</h2>
      <Button onClick={onCreate}>
        <IconPlus data-icon="inline-start" />
        Neue Quelle
      </Button>
    </div>
  );
}
