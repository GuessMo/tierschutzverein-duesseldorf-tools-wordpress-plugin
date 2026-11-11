import { useState } from "react";
import { IconPlus } from "@tabler/icons-react";
import { Button, PageContainer } from "@web-app-kit/frontend/ui";
import { animalsApi } from "@/api";
import { NEW_ANIMAL_HASH, useListView, useResource } from "@/hooks";
import { EMPTY_FILTER } from "@/lib";
import type { AnimalFilter } from "@/types";
import { AnimalFilters } from "./AnimalFilters";
import { AnimalResults } from "./AnimalResults";
import type { AnimalsScreenProps } from "./list.types";

export function AnimalsScreen({ options }: AnimalsScreenProps) {
  const animals = useResource(animalsApi.list);
  const [filter, setFilter] = useState<AnimalFilter>(EMPTY_FILTER);
  const [view, setView] = useListView();

  return (
    <PageContainer size="lg" className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-xl font-semibold">Tiere</h1>
        <Button asChild>
          <a href={NEW_ANIMAL_HASH}>
            <IconPlus data-icon="inline-start" />
            Neues Tier
          </a>
        </Button>
      </div>
      <AnimalFilters options={options} filter={filter} onChange={setFilter} />
      <AnimalResults
        animals={animals}
        options={options}
        filter={filter}
        view={view}
        onViewChange={setView}
        onResetFilter={() => setFilter(EMPTY_FILTER)}
      />
    </PageContainer>
  );
}
