export type Route =
  | { view: "list" }
  | { view: "stats" }
  | { view: "new" }
  | { view: "edit"; id: number };
