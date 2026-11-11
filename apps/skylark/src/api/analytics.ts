import type { AnalyticsData, AnalyticsRange } from "@/types";
import { send } from "./client";

export const analyticsApi = {
  data: (range: AnalyticsRange) => send<AnalyticsData>("GET", `skylark/v1/data?range=${range}`),
};
