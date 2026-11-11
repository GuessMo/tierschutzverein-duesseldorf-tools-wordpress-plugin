import { useEffect, useState } from "react";
import type { AcademyRoute } from "@/types";

const LESSON = /^#lesson\/(\d+)$/;
const TOPIC = /^#topic\/([\w-]+)$/;

function readRoute(): AcademyRoute {
  const lesson = LESSON.exec(window.location.hash);
  if (lesson) return { view: "lesson", id: Number(lesson[1]) };
  const topic = TOPIC.exec(window.location.hash);
  return { view: "list", topic: topic?.[1] ?? "" };
}

export function lessonHash(id: number): string {
  return `#lesson/${id}`;
}

export function topicHash(slug: string): string {
  return slug ? `#topic/${slug}` : "#";
}

export function useAcademyRoute(): AcademyRoute {
  const [route, setRoute] = useState(readRoute);
  useEffect(() => {
    const onChange = () => setRoute(readRoute());
    window.addEventListener("hashchange", onChange);
    return () => window.removeEventListener("hashchange", onChange);
  }, []);
  return route;
}
