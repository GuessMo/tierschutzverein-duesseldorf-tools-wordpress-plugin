import type { ComparePair, Comparisons } from "./compare";
import type { Widgets } from "./widgets";

export type AnalyticsRange = 7 | 30 | 90;

export interface CountRow {
  label: string;
  total: number;
}

export interface Totals {
  pageviews: number;
  visitors: number;
  bounceRate: number;
  avgDuration: number;
}

export interface Series {
  pageviews: CountRow[];
  visitors: CountRow[];
  duration: CountRow[];
  seoScore: CountRow[];
}

export interface Breakdowns {
  pages: CountRow[];
  referrers: CountRow[];
  notFound: CountRow[];
  countries: CountRow[];
  devices: CountRow[];
  browsers: CountRow[];
  systems: CountRow[];
}

export interface RedirectRoute {
  code: string;
  label: string;
  total: number;
  series: CountRow[];
  top: CountRow[];
  all: CountRow[];
  counts: RedirectCounts;
  compare: ComparePair[];
}

export interface RedirectCounts {
  month: number;
  months: CountRow[];
  years: CountRow[];
  all: number;
}

export interface SeoRow {
  title: string;
  url: string;
  score: number;
  delta: number;
}

export interface SeoLists {
  worst: SeoRow[];
  best: SeoRow[];
  below: SeoRow[];
  deltas: SeoRow[];
}

export interface AnalyticsData {
  range: AnalyticsRange;
  totals: Totals;
  series: Series;
  breakdowns: Breakdowns;
  redirects: RedirectRoute[];
  seo: SeoLists;
  compare: Comparisons;
  widgets: Widgets;
  mapUrl: string;
  refresh: number;
}
