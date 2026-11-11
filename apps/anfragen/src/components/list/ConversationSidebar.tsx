import { ErrorMessage, LoadingRows, StateMessage } from "@/components/common";
import { ConversationItem } from "./ConversationItem";
import { HouseFilter } from "./HouseFilter";
import type { ConversationSidebarProps } from "./list.types";
import { ListPager } from "./ListPager";
import { SearchField } from "./SearchField";
import { StatusFilter } from "./StatusFilter";

function Items({ state, selectedId, onSelect }: ConversationSidebarProps) {
  const { list } = state;
  if (list.error) return <ErrorMessage message={list.error} onRetry={() => void list.reload()} />;
  if (!list.data) return <LoadingRows label="Anfragen werden geladen" count={6} />;
  if (list.data.items.length === 0) {
    return <StateMessage title="Keine Anfragen" description="In diesem Ordner liegt nichts." />;
  }
  return (
    <ul className="flex flex-col gap-0.5">
      {list.data.items.map((item) => (
        <li key={item.id}>
          <ConversationItem
            item={item}
            isSelected={item.id === selectedId}
            onSelect={() => onSelect(item.id)}
          />
        </li>
      ))}
    </ul>
  );
}

export function ConversationSidebar(props: ConversationSidebarProps) {
  const { filter, update, setPage, list } = props.state;
  return (
    <div className="flex flex-col gap-3 p-3">
      <StatusFilter
        value={filter.status}
        counts={list.data?.counts ?? null}
        onChange={(status) => update({ status })}
      />
      <HouseFilter
        value={filter.breed}
        houses={list.data?.houses ?? []}
        onChange={(breed) => update({ breed })}
      />
      <SearchField value={filter.search} onChange={(search) => update({ search })} />
      <Items {...props} />
      <ListPager page={filter.page} pages={list.data?.pages ?? 1} onPage={setPage} />
    </div>
  );
}
