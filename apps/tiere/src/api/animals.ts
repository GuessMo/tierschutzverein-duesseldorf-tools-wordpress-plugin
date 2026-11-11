import type {
  AnimalImage,
  AnimalListItem,
  AnimalOptions,
  AnimalPayload,
  AnimalRecord,
  AnimalStats,
} from "@/types";
import { send, upload } from "./client";

const BASE = "tiere";

function uploadImage(file: File, animalId: number | null): Promise<AnimalImage> {
  const form = new FormData();
  form.append("file", file);
  if (animalId) form.append("animal_id", String(animalId));
  return upload<AnimalImage>(`${BASE}/media`, form);
}

export const animalsApi = {
  list: () => send<AnimalListItem[]>("GET", BASE),
  options: () => send<AnimalOptions>("GET", `${BASE}/options`),
  stats: () => send<AnimalStats>("GET", `${BASE}/statistik`),
  get: (id: number) => send<AnimalRecord>("GET", `${BASE}/${id}`),
  create: (payload: AnimalPayload) => send<AnimalRecord>("POST", BASE, payload),
  update: (id: number, payload: AnimalPayload) =>
    send<AnimalRecord>("PATCH", `${BASE}/${id}`, payload),
  restore: (id: number) => send<AnimalRecord>("POST", `${BASE}/${id}/restore`),
  uploadImage,
};
