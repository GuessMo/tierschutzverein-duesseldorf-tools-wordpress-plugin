import type { AcademyIndex, Lesson } from "@/types";
import { send } from "./client";

export const academyApi = {
  index: () => send<AcademyIndex>("GET", "academy"),
  lesson: (slug: string) => send<Lesson>("GET", `academy/${slug}`),
};
