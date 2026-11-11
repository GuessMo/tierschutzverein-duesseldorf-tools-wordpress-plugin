import type { Icon } from "@tabler/icons-react";
import { IconApps, IconBooks, IconMessages, IconPaw, IconWorld } from "@tabler/icons-react";

const SYMBOLS: Record<string, Icon> = {
  world: IconWorld,
  paw: IconPaw,
  messages: IconMessages,
  books: IconBooks,
};

export function symbolFor(name: string): Icon {
  return SYMBOLS[name] ?? IconApps;
}
