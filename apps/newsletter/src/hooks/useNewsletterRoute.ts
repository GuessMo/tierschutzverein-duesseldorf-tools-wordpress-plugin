import { useEffect, useState } from "react";
import type { NewsletterRoute } from "@/types";

const ISSUE = /^#ausgabe\/(\d+)$/;

export const ISSUES_HASH = "#";
export const SUBSCRIBERS_HASH = "#abonnenten";
export const BLOCKS_HASH = "#bloecke";

export function issueHash(id: number): string {
  return `#ausgabe/${id}`;
}

function readRoute(): NewsletterRoute {
  const { hash } = window.location;
  if (hash === SUBSCRIBERS_HASH) return { view: "subscribers" };
  if (hash === BLOCKS_HASH) return { view: "blocks" };
  const issue = ISSUE.exec(hash);
  return issue ? { view: "issue", id: Number(issue[1]) } : { view: "issues" };
}

export function useNewsletterRoute(): NewsletterRoute {
  const [route, setRoute] = useState(readRoute);
  useEffect(() => {
    const onChange = () => setRoute(readRoute());
    window.addEventListener("hashchange", onChange);
    return () => window.removeEventListener("hashchange", onChange);
  }, []);
  return route;
}
