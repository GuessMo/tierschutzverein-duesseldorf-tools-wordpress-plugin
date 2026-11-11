import type { TermRowProps } from "./collection.types";
import { ActionMenu } from "./ActionMenu";
import { termActions } from "./menuActions";

const INDENT_REM = 1;

export function TermRow({ taxonomy, term, depth, onOpen }: TermRowProps) {
  return (
    <li
      className="flex min-h-8 items-center gap-1 pr-0 text-sm"
      style={{ paddingLeft: `${0.5 + depth * INDENT_REM}rem` }}
    >
      <span className="min-w-0 flex-1 truncate">{term.name}</span>
      <ActionMenu
        label={`Aktionen für Begriff ${term.name}`}
        actions={termActions(taxonomy, term, onOpen)}
      />
    </li>
  );
}
