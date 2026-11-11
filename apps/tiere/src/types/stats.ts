import type { CountRow } from "@tsvd/tool-controls";

export interface StatsTrend {
  thisMonth: number;
  average: number;
  delta: number | null;
}

export interface ListedVsRow {
  label: string;
  listed: number;
  requests: number;
}

export interface RequestStats {
  total: number;
  trend: StatsTrend;
  interest: number;
  private: number;
  missing: number;
  byType: CountRow[];
  months: CountRow[];
  residence: CountRow[];
  housing: CountRow[];
  outdoor: CountRow[];
  species: CountRow[];
  breed: CountRow[];
  topAnimals: CountRow[];
  listedVs: ListedVsRow[];
}

export interface StockStats {
  byStatus: CountRow[];
  adopted: CountRow[];
  sources: CountRow[];
}

export interface TimeKpi {
  label: string;
  value: string;
  sub: string;
}

export interface TimeCompareRow {
  label: string;
  verein: number;
  person: number;
}

export interface ResponseTimes {
  kpis: TimeKpi[];
  first: CountRow[];
  compare: TimeCompareRow[];
}

export interface AnimalStats {
  requests: RequestStats;
  stock: StockStats;
  responseTimes: ResponseTimes | null;
}
