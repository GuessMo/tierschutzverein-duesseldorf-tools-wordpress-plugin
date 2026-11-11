import type { AcademyIndex, Lesson } from "@/types";
import { send } from "./client";

export const academyApi = {
  index: () => send<AcademyIndex>("GET", "academy"),
  lesson: (id: number) => send<Lesson>("GET", `academy/${id}`),
};
