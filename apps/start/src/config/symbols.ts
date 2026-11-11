import type { Icon } from "@tabler/icons-react";
import {
  IconApps,
  IconBooks,
  IconChartBar,
  IconMessages,
  IconPaw,
  IconSchool,
  IconWorld,
} from "@tabler/icons-react";

const SYMBOLS: Record<string, Icon> = {
  world: IconWorld,
  paw: IconPaw,
  messages: IconMessages,
  books: IconBooks,
  chart: IconChartBar,
  school: IconSchool,
};

export function symbolFor(name: string): Icon {
  return SYMBOLS[name] ?? IconApps;
}
