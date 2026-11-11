import { useCallback, useMemo, type MouseEvent } from "react";
import { Badge } from "@web-app-kit/frontend/ui";
import { academyApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { useResource } from "@/hooks";
import { renderLesson } from "@/lib";
import type { TocItem } from "@/types";
import { LessonActions } from "./LessonActions";

const TOC_LINK_CLASSES = [
  "rounded-sm hover:text-foreground hover:underline focus-visible:outline-2",
  "focus-visible:outline-ring active:text-primary",
].join(" ");
import type { LessonViewProps } from "./lessons.types";

function jumpTo(event: MouseEvent<HTMLAnchorElement>, id: string) {
  event.preventDefault();
  const target = document.getElementById(id);
  if (!target) return;
  target.setAttribute("tabindex", "-1");
  target.scrollIntoView({ behavior: "smooth", block: "start" });
  target.focus({ preventScroll: true });
}

function LessonToc({ toc }: { toc: TocItem[] }) {
  if (toc.length < 2) return null;
  return (
    <nav
      aria-label="Inhalt"
      className="flex flex-col gap-2 self-start rounded-xl border p-4 text-sm lg:sticky lg:top-4"
    >
      <span className="font-medium">Inhalt</span>
      <ol className="flex list-decimal flex-col gap-1 pl-5 text-muted-foreground">
        {toc.map((item) => (
          <li key={item.id}>
            <a
              href={`#${item.id}`}
              onClick={(event) => jumpTo(event, item.id)}
              className={TOC_LINK_CLASSES}
            >
              {item.text}
            </a>
          </li>
        ))}
      </ol>
    </nav>
  );
}

export function LessonView({ slug }: LessonViewProps) {
  const load = useCallback(() => academyApi.lesson(slug), [slug]);
  const lesson = useResource(load);
  const rendered = useMemo(
    () => (lesson.data ? renderLesson(lesson.data.markdown, lesson.data.blocks) : null),
    [lesson.data],
  );
  if (lesson.error)
    return <ErrorMessage message={lesson.error} onRetry={() => void lesson.reload()} />;
  if (!lesson.data || !rendered) return <LoadingRows label="Anleitung wird geladen" count={6} />;
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
        <div className="academy-prose" dangerouslySetInnerHTML={{ __html: rendered.html }} />
        <LessonToc toc={rendered.toc} />
      </div>
    </article>
  );
}
