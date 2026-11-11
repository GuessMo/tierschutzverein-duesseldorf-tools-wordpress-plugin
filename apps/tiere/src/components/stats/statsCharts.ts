import {
  ALL_TYPES,
  formatNumber,
  slotColor,
  type ChartPanelSpec,
  type ChartType,
  type CountRow,
} from "@tsvd/tool-controls";

export function statsChart(
  id: string,
  label: string,
  rows: CountRow[],
  slot: number,
  fallback: ChartType,
): ChartPanelSpec {
  return {
    id: `tiere-${id}`,
    label,
    rows,
    color: slotColor(slot as 1),
    fallback,
    types: ALL_TYPES,
    format: formatNumber,
  };
}
