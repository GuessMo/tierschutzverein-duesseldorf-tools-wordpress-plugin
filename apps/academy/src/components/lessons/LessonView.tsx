import { useCallback, type MouseEvent } from "react";
import { IconArrowLeft, IconPencil, IconPrinter } from "@tabler/icons-react";
import { Badge, Button } from "@web-app-kit/frontend/ui";
import { academyApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { topicHash, useResource } from "@/hooks";
import type { Lesson } from "@/types";
import type { LessonViewProps } from "./lessons.types";

function jumpTo(event: MouseEvent<HTMLAnchorElement>, id: string) {
  event.preventDefault();
  const target = document.getElementById(id);
  if (!target) return;
  target.setAttribute("tabindex", "-1");
  target.scrollIntoView({ behavior: "smooth", block: "start" });
  target.focus({ preventScroll: true });
}

function LessonToc({ lesson }: { lesson: Lesson }) {
  if (lesson.toc.length < 2) return null;
  return (
    <nav
      aria-label="Inhalt"
      className="flex flex-col gap-2 self-start rounded-xl border p-4 text-sm lg:sticky lg:top-4 print:hidden"
    >
      <span className="font-medium">Inhalt</span>
      <ol className="flex list-decimal flex-col gap-1 pl-5 text-muted-foreground">
        {lesson.toc.map((item) => (
          <li key={item.id}>
            <a href={`#${item.id}`} onClick={(event) => jumpTo(event, item.id)} className="rounded-sm hover:text-foreground hover:underline focus-visible:outline-2 focus-visible:outline-ring active:text-primary">
              {item.text}
            </a>
          </li>
        ))}
      </ol>
    </nav>
  );
}

function LessonActions({ lesson }: { lesson: Lesson }) {
  const backHref = topicHash(lesson.topics[0]?.slug ?? "");
  return (
    <div className="flex flex-wrap items-center gap-2 print:hidden">
      <Button asChild variant="ghost" size="sm">
        <a href={backHref}>
          <IconArrowLeft data-icon="inline-start" />
          Zur Übersicht
        </a>
      </Button>
      <Button variant="ghost" size="sm" onClick={() => window.print()}>
        <IconPrinter data-icon="inline-start" />
        Drucken
      </Button>
      {lesson.editUrl && (
        <Button asChild variant="outline" size="sm">
          <a href={lesson.editUrl} target="_blank" rel="noreferrer">
            <IconPencil data-icon="inline-start" />
            Bearbeiten
          </a>
        </Button>
      )}
    </div>
  );
}

export function LessonView({ id }: LessonViewProps) {
  const load = useCallback(() => academyApi.lesson(id), [id]);
  const lesson = useResource(load);
  if (lesson.error) return <ErrorMessage message={lesson.error} onRetry={() => void lesson.reload()} />;
  if (!lesson.data) return <LoadingRows label="Anleitung wird geladen" count={6} />;
  return (
    <article className="flex flex-col gap-6">
      <LessonActions lesson={lesson.data} />
      <header className="flex flex-col gap-2">
        <h1 className="text-2xl font-semibold text-balance">{lesson.data.title}</h1>
        <div className="flex flex-wrap gap-1">
          {lesson.data.topics.map((topic) => (
            <Badge key={topic.slug} variant="secondary">
              {topic.name}
            </Badge>
          ))}
        </div>
      </header>
      <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_16rem]">
        <div className="academy-prose" dangerouslySetInnerHTML={{ __html: lesson.data.html }} />
        <LessonToc lesson={lesson.data} />
      </div>
    </article>
  );
}
