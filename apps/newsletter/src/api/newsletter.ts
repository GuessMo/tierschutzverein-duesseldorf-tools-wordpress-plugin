import type {
  IssueDetail,
  IssueIndex,
  NewsletterBlock,
  SendResult,
  Subscriber,
  UploadedImage,
} from "@/types";
import { send, upload } from "./client";

const BASE = "newsletter";

function uploadImage(file: File): Promise<UploadedImage> {
  const form = new FormData();
  form.append("file", file);
  return upload<UploadedImage>(`${BASE}/media`, form);
}

export const newsletterApi = {
  index: () => send<IssueIndex>("GET", BASE),
  create: (title: string) => send<IssueDetail>("POST", BASE, { title }),
  get: (id: number) => send<IssueDetail>("GET", `${BASE}/${id}`),
  save: (id: number, title: string, blocks: NewsletterBlock[]) =>
    send<IssueDetail>("PATCH", `${BASE}/${id}`, { title, blocks }),
  remove: (id: number) => send<{ deleted: boolean }>("DELETE", `${BASE}/${id}`),
  preview: (id: number) => send<{ html: string }>("GET", `${BASE}/${id}/preview`),
  send: (id: number, mode: "test" | "all") =>
    send<SendResult>("POST", `${BASE}/${id}/send`, { mode }),
  subscribers: () => send<Subscriber[]>("GET", `${BASE}/subscribers`),
  saveSubscribers: (ids: number[]) =>
    send<{ count: number }>("PUT", `${BASE}/subscribers`, { ids }),
  uploadImage,
};
