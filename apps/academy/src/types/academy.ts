export interface Topic {
  slug: string;
  name: string;
}

export interface LessonSummary {
  slug: string;
  title: string;
  excerpt: string;
  topics: Topic[];
  updated: string;
}

export interface TocItem {
  id: string;
  text: string;
}

export interface Lesson extends LessonSummary {
  markdown: string;
  blocks: Record<string, string>;
}

export interface RenderedLesson {
  html: string;
  toc: TocItem[];
}

export interface AcademyIndex {
  topics: Topic[];
  lessons: LessonSummary[];
}

export type AcademyRoute = { view: "list"; topic: string } | { view: "lesson"; slug: string };
