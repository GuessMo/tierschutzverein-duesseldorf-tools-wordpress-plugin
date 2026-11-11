import type { CountRow } from "@tsvd/tool-controls";

export interface ConsentDay {
  label: string;
  accepted: number;
  declined: number;
  customized: number;
}

export interface Widgets {
  animals: { species: CountRow[]; status: CountRow[] };
  consent: {
    combined: ConsentDay[];
    accepted: CountRow[];
    declined: CountRow[];
    customized: CountRow[];
  };
  letters: { top: CountRow[]; never: CountRow[] };
  news: { top: CountRow[]; categories: CountRow[] };
  projects: CountRow[];
  material: { views: CountRow[]; downloads: CountRow[]; never: CountRow[] };
}
