import type {
  AnimalDraft,
  AnimalImage,
  AnimalPayload,
  AnimalRecord,
  AnimalTdw,
  PostStatus,
} from "@/types";
import { isoWeek } from "./isoWeek";

const UNKNOWN = "unknown";

const EMPTY_FIELDS: Omit<AnimalDraft, "tdw"> = {
  status: "draft",
  name: "",
  teo_id: "",
  description: "",
  birthday_status: UNKNOWN,
  birthday: "",
  life_stage: "undefined",
  color: null,
  size_cm: "small",
  gender: UNKNOWN,
  castrated: UNKNOWN,
  vaccinated: UNKNOWN,
  chipped: UNKNOWN,
  breeds: [],
  companions: [],
  record_type: "vermittlung",
  mediator: "tierschutzverein",
  adoption_status: "not_for_adoption",
  participates_in_nori: false,
  missing_status: "missing",
  missing: {},
  private_contact: {},
  adoption_requirements: [],
  adoption_date: "",
  owner_feedback_date: "",
  adoption_source: "",
  adoption_source_other: "",
  show_in_frontend: true,
  owner_text: "",
  featured_image: null,
  images: [],
  owner_photos: [],
  interest_seed: null,
};

function currentTdw(): AnimalTdw {
  const today = new Date();
  return { enabled: false, year: today.getFullYear(), week: isoWeek(today), text: "", article_image: null };
}

function imageIds(images: AnimalImage[]): number[] {
  return images.map((image) => image.id);
}

export function emptyDraft(): AnimalDraft {
  return { ...EMPTY_FIELDS, tdw: currentTdw() };
}

export function recordToDraft(record: AnimalRecord): AnimalDraft {
  const draft = emptyDraft();
  for (const key of Object.keys(draft) as (keyof AnimalDraft)[]) {
    if (key in record) Object.assign(draft, { [key]: record[key as keyof AnimalRecord] });
  }
  draft.interest_seed = record.interest ? record.interest.seed : null;
  return draft;
}

function tdwPayload(tdw: AnimalTdw): AnimalPayload["tdw"] {
  const { article_image, ...rest } = tdw;
  return { ...rest, article_image_id: article_image?.id ?? 0 };
}

export function draftToPayload(draft: AnimalDraft, initialStatus: PostStatus): AnimalPayload {
  const { featured_image, images, owner_photos, tdw, interest_seed, status, ...fields } = draft;
  return {
    ...fields,
    ...(status === initialStatus ? {} : { status }),
    ...(interest_seed === null ? {} : { interest_seed }),
    featured_image_id: featured_image?.id ?? 0,
    image_ids: imageIds(images),
    owner_photo_ids: imageIds(owner_photos),
    tdw: tdwPayload(tdw),
  };
}
