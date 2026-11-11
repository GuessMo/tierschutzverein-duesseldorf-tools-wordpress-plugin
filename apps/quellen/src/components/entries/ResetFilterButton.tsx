import { IconX } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";

export function ResetFilterButton({ onReset }: { onReset: () => void }) {
  return (
    <Button variant="ghost" className="justify-self-start" onClick={onReset}>
      <IconX data-icon="inline-start" />
      Filter zurücksetzen
    </Button>
  );
}
