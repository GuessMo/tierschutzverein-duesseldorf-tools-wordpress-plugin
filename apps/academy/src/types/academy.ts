export interface Topic {
  slug: string;
  name: string;
}

export interface LessonSummary {
  id: number;
  title: string;
  excerpt: string;
  topics: Topic[];
  modified: string;
}

export interface TocItem {
  id: string;
  text: string;
}

export interface Lesson extends LessonSummary {
  html: string;
  toc: TocItem[];
  editUrl: string;
}

export interface AcademyIndex {
  topics: Topic[];
  lessons: LessonSummary[];
  canManage: boolean;
  newUrl: string;
}

export type AcademyRoute = { view: "list"; topic: string } | { view: "lesson"; id: number };
