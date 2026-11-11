import type { AnalyticsData, AnalyticsRange, SeoRow } from "@/types";

export interface SectionProps {
  data: AnalyticsData;
}

export interface RangeToggleProps {
  value: AnalyticsRange;
  onChange: (range: AnalyticsRange) => void;
}

export interface SeoListProps {
  rows: SeoRow[];
  showDelta?: boolean;
}
