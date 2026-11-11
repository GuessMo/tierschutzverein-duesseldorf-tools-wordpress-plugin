export interface CompareRow {
  day: number;
  a: number;
  b: number;
}

export interface ComparePair {
  key: string;
  label: string;
  gap: number;
  aMonth: string;
  bMonth: string;
  a: number;
  b: number;
  delta: number | null;
  rows: CompareRow[];
}

export interface Comparisons {
  pageviews: ComparePair[];
  visitors: ComparePair[];
  duration: ComparePair[];
  seoScore: ComparePair[];
}
