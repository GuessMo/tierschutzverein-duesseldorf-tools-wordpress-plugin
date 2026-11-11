import { useEffect, useState } from "react";
import type { AcademyRoute } from "@/types";

const LESSON = /^#lesson\/([a-z0-9-]+)$/;
const TOPIC = /^#topic\/([\w-]+)$/;

function readRoute(): AcademyRoute {
  const lesson = LESSON.exec(window.location.hash);
  if (lesson?.[1]) return { view: "lesson", slug: lesson[1] };
  const topic = TOPIC.exec(window.location.hash);
  return { view: "list", topic: topic?.[1] ?? "" };
}

export function lessonHash(slug: string): string {
  return `#lesson/${slug}`;
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
