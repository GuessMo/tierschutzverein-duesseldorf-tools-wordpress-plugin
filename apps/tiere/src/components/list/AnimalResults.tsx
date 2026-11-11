import { Button } from "@web-app-kit/frontend/ui";
import { ErrorMessage, LoadingRows, StateMessage } from "@/components/common";
import { NEW_ANIMAL_HASH } from "@/hooks";
import { filterAnimals } from "@/lib";
import { AnimalCollection } from "./AnimalCollection";
import type { AnimalResultsProps } from "./list.types";
import { ViewToggle } from "./ViewToggle";

function countLabel(shown: number, total: number): string {
  if (shown === total) return `${total} Tiere`;
  return `${shown} von ${total} Tieren`;
}

export function AnimalResults(props: AnimalResultsProps) {
  const { animals, options, filter, view, onViewChange, onResetFilter } = props;
  if (animals.error) {
    return <ErrorMessage message={animals.error} onRetry={() => void animals.reload()} />;
  }
  if (!animals.data) return <LoadingRows label="Tiere werden geladen" count={6} />;
  if (animals.data.length === 0) {
    return (
      <StateMessage
        title="Noch keine Tiere"
        description="Leg das erste Tier an. Es bleibt ein Entwurf, bis du es veröffentlichst."
        action={
          <Button asChild>
            <a href={NEW_ANIMAL_HASH}>Neues Tier</a>
          </Button>
        }
      />
    );
  }
  const shown = filterAnimals(animals.data, filter);
  return (
    <div className="flex flex-col gap-3">
      <div className="flex items-center justify-between gap-3">
        <p role="status" className="text-sm text-muted-foreground">
          {countLabel(shown.length, animals.data.length)}
        </p>
        <ViewToggle view={view} onChange={onViewChange} />
      </div>
      {shown.length === 0 ? (
        <StateMessage
          title="Kein Tier passt zu den Filtern"
          description="Ändere die Suche oder setze die Filter zurück."
          action={
            <Button variant="outline" onClick={onResetFilter}>
              Filter zurücksetzen
            </Button>
          }
        />
      ) : (
        <AnimalCollection animals={shown} options={options} view={view} />
      )}
    </div>
  );
}
