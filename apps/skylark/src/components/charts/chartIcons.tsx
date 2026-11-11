import {
  IconChartBar,
  IconChartDonut3,
  IconChartLine,
  IconChartPie,
  IconWorld,
} from "@tabler/icons-react";
import type { ReactNode } from "react";
import type { ChartType } from "./chartTypes";

const ICONS: Record<ChartType, ReactNode> = {
  vbars: <IconChartBar aria-hidden />,
  hbars: <IconChartBar aria-hidden className="rotate-90 -scale-x-100" />,
  line: <IconChartLine aria-hidden />,
  donut: <IconChartDonut3 aria-hidden />,
  pie: <IconChartPie aria-hidden />,
  map: <IconWorld aria-hidden />,
};

export function chartIcon(type: ChartType): ReactNode {
  return ICONS[type];
}
