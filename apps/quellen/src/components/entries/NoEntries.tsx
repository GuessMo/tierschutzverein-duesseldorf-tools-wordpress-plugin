import { Button } from "@web-app-kit/frontend/ui";
import { StateMessage } from "@/components/common";

export function NoEntries({ onCreate }: { onCreate: () => void }) {
  return (
    <StateMessage
      title="Noch keine Quellen"
      description="Lege die erste Quelle an: Titel, Link und Herausgeber reichen für den Anfang."
      action={
        <Button variant="outline" onClick={onCreate}>
          Quelle anlegen
        </Button>
      }
    />
  );
}
