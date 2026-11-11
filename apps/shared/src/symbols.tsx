import type { ReactNode } from "react";
import {
  IconApps,
  IconBooks,
  IconChartBar,
  IconMessages,
  IconPaw,
  IconSchool,
  IconWorld,
  type Icon,
} from "@tabler/icons-react";

const SYMBOLS: Record<string, Icon> = {
  world: IconWorld,
  paw: IconPaw,
  messages: IconMessages,
  books: IconBooks,
  chart: IconChartBar,
  school: IconSchool,
};

export function symbolIcon(name: string, color: string): ReactNode {
  const Symbol = SYMBOLS[name] ?? IconApps;
  return <Symbol stroke={1.5} style={{ color }} />;
}
