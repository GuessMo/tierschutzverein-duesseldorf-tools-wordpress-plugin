import type { AnimalRecord, PostStatus } from "./animal";

export type DraftFields =
  | "name"
  | "teo_id"
  | "description"
  | "birthday_status"
  | "birthday"
  | "life_stage"
  | "color"
  | "size_cm"
  | "gender"
  | "castrated"
  | "vaccinated"
  | "chipped"
  | "breeds"
  | "companions"
  | "record_type"
  | "mediator"
  | "adoption_status"
  | "participates_in_nori"
  | "missing_status"
  | "missing"
  | "private_contact"
  | "adoption_requirements"
  | "adoption_date"
  | "owner_feedback_date"
  | "adoption_source"
  | "adoption_source_other"
  | "show_in_frontend"
  | "owner_text"
  | "featured_image"
  | "images"
  | "owner_photos"
  | "tdw";

export type AnimalDraft = Pick<AnimalRecord, DraftFields> & {
  status: PostStatus;
  interest_seed: number | null;
};

export type DraftPatch = Partial<AnimalDraft>;

export interface AnimalPayload
  extends Omit<
    AnimalDraft,
    "featured_image" | "images" | "owner_photos" | "tdw" | "interest_seed" | "status"
  > {
  status?: PostStatus;
  featured_image_id: number;
  image_ids: number[];
  owner_photo_ids: number[];
  interest_seed?: number;
  tdw: {
    enabled: boolean;
    year: number;
    week: number;
    text: string;
    article_image_id: number;
  };
}
