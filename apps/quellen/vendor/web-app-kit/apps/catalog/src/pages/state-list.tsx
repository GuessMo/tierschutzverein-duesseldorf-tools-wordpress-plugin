import type { CatalogState } from "../catalog/catalog.types";

export function StateList({ states }: { states: CatalogState[] }) {
  return (
    <dl className="grid max-w-3xl grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-[12rem_1fr]">
      {states.map((state) => (
        <div key={state.name} className="contents">
          <dt className="font-medium">{state.name}</dt>
          <dd className="text-muted-foreground">{state.description}</dd>
        </div>
      ))}
    </dl>
  );
}
