import { ErrorMessage, LoadingRows, StateMessage } from "@/components/common";
import { ListPager } from "@/components/list";
import { useRelayed } from "@/hooks";
import { RelayedFilters } from "./RelayedFilters";
import { RelayedIntro } from "./RelayedIntro";
import { RelayedRow } from "./RelayedRow";

function totalLabel(total: number): string {
  return total === 1 ? "1 Nachricht" : `${total} Nachrichten`;
}

export function RelayedScreen() {
  const { filter, update, setPage, list } = useRelayed();
  return (
    <div className="flex flex-col gap-6">
      <h1 className="text-2xl font-semibold">Weitergeleitet an Halter</h1>
      <RelayedIntro retentionDays={list.data?.retentionDays ?? 90} />
      <RelayedFilters filter={filter} months={list.data?.months ?? []} onChange={update} />
      {list.error && <ErrorMessage message={list.error} onRetry={() => void list.reload()} />}
      {!list.data && !list.error && <LoadingRows label="Nachrichten werden geladen" count={4} />}
      {list.data && list.data.items.length === 0 && (
        <StateMessage
          title="Keine Nachrichten"
          description="Für diese Auswahl gibt es keine Einträge."
        />
      )}
      {list.data && list.data.items.length > 0 && (
        <>
          <p className="text-sm text-muted-foreground">{totalLabel(list.data.total)}</p>
          <ul className="flex flex-col gap-3">
            {list.data.items.map((item) => (
              <RelayedRow key={item.id} item={item} />
            ))}
          </ul>
          <ListPager page={filter.page} pages={list.data.pages} onPage={setPage} />
        </>
      )}
    </div>
  );
}
