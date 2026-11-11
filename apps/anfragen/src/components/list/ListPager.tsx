import { IconChevronLeft, IconChevronRight } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { ListPagerProps } from "./list.types";

export function ListPager({ page, pages, onPage }: ListPagerProps) {
  if (pages <= 1) return null;
  return (
    <div className="flex items-center justify-between gap-2 text-sm text-muted-foreground">
      <Button
        variant="ghost"
        size="icon-sm"
        aria-label="Vorherige Seite"
        disabled={page <= 1}
        onClick={() => onPage(page - 1)}
      >
        <IconChevronLeft aria-hidden />
      </Button>
      <span>
        Seite {page} von {pages}
      </span>
      <Button
        variant="ghost"
        size="icon-sm"
        aria-label="Nächste Seite"
        disabled={page >= pages}
        onClick={() => onPage(page + 1)}
      >
        <IconChevronRight aria-hidden />
      </Button>
    </div>
  );
}
