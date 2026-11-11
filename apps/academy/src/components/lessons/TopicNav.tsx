import { IconSearch } from "@tabler/icons-react";
import {
  cn,
  INTERACTIVE_SURFACE_CLASSES,
  InputGroup,
  InputGroupAddon,
  InputGroupInput,
} from "@web-app-kit/frontend/ui";
import { topicHash } from "@/hooks";
import type { TopicNavProps } from "./lessons.types";

const LINK_CLASSES = "flex w-full items-center justify-between gap-2 rounded-md px-3 py-2 text-sm";

function TopicLink({
  slug,
  name,
  count,
  isActive,
}: {
  slug: string;
  name: string;
  count: number;
  isActive: boolean;
}) {
  return (
    <a
      href={topicHash(slug)}
      aria-current={isActive ? "page" : undefined}
      className={cn(INTERACTIVE_SURFACE_CLASSES, LINK_CLASSES)}
    >
      <span className="truncate">{name}</span>
      <span className="text-xs text-muted-foreground tabular-nums">{count}</span>
    </a>
  );
}

export function TopicNav({ topics, lessons, activeTopic, search, onSearch }: TopicNavProps) {
  const countFor = (slug: string) =>
    lessons.filter((lesson) => lesson.topics.some((topic) => topic.slug === slug)).length;
  return (
    <div className="flex flex-col gap-3 p-3">
      <InputGroup>
        <InputGroupAddon>
          <IconSearch aria-hidden />
        </InputGroupAddon>
        <InputGroupInput
          type="search"
          aria-label="Anleitungen durchsuchen"
          placeholder="Anleitung suchen"
          value={search}
          onChange={(event) => onSearch(event.target.value)}
        />
      </InputGroup>
      <ul className="flex flex-col gap-0.5">
        <li>
          <TopicLink
            slug=""
            name="Alle Themen"
            count={lessons.length}
            isActive={activeTopic === ""}
          />
        </li>
        {topics.map((topic) => (
          <li key={topic.slug}>
            <TopicLink
              slug={topic.slug}
              name={topic.name}
              count={countFor(topic.slug)}
              isActive={activeTopic === topic.slug}
            />
          </li>
        ))}
      </ul>
    </div>
  );
}
