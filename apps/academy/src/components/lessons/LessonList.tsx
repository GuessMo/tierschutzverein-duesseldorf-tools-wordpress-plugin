import { Badge, Card } from "@web-app-kit/frontend/ui";
import { StateMessage } from "@/components/common";
import { lessonHash } from "@/hooks";
import type { LessonSummary } from "@/types";
import type { LessonListProps } from "./lessons.types";

function matches(lesson: LessonSummary, topic: string, search: string): boolean {
  const inTopic = topic === "" || lesson.topics.some((item) => item.slug === topic);
  const needle = search.trim().toLowerCase();
  const text = `${lesson.title} ${lesson.excerpt}`.toLowerCase();
  return inTopic && (needle === "" || text.includes(needle));
}

function LessonCard({ lesson }: { lesson: LessonSummary }) {
  return (
    <Card interactive asChild className="h-full gap-2 px-4">
      <a href={lessonHash(lesson.slug)}>
        <span className="font-semibold">{lesson.title}</span>
        <span className="line-clamp-3 text-sm text-muted-foreground">{lesson.excerpt}</span>
        <span className="flex flex-wrap gap-1">
          {lesson.topics.map((topic) => (
            <Badge key={topic.slug} variant="secondary">
              {topic.name}
            </Badge>
          ))}
        </span>
      </a>
    </Card>
  );
}

export function LessonList({ index, topic, search }: LessonListProps) {
  const lessons = index.lessons.filter((lesson) => matches(lesson, topic, search));
  const title = index.topics.find((item) => item.slug === topic)?.name ?? "Alle Anleitungen";
  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-xl font-semibold">{title}</h1>
      </div>
      {lessons.length === 0 ? (
        <StateMessage
          title="Keine Anleitung gefunden"
          description="Ändere die Suche oder wähle ein anderes Thema."
        />
      ) : (
        <ul className="grid gap-4 sm:grid-cols-2">
          {lessons.map((lesson) => (
            <li key={lesson.slug}>
              <LessonCard lesson={lesson} />
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
