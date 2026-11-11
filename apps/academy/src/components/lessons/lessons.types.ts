import type { AcademyIndex, LessonSummary, Topic } from "@/types";

export interface TopicNavProps {
  topics: Topic[];
  lessons: LessonSummary[];
  activeTopic: string;
  search: string;
  onSearch: (search: string) => void;
}

export interface LessonListProps {
  index: AcademyIndex;
  topic: string;
  search: string;
}

export interface LessonViewProps {
  id: number;
}
