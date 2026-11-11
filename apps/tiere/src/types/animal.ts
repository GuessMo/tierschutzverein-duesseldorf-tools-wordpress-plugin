export type PostStatus = "publish" | "future" | "draft" | "pending" | "private" | "trash";

export type SaveStatus = "draft" | "pending" | "publish";

export type RecordType = "vermittlung" | "vermisst";

export type MissingStatus = "missing" | "found" | "reunited";

export type FocusState = "set" | "missing" | "no_image";

export interface AnimalImage {
  id: number;
  thumb: string;
  large: string;
  alt: string;
}

export interface Companion {
  id: number;
  title: string;
  internal_id: string;
  breed: string;
  thumb: string;
}

export interface AnimalListItem {
  id: number;
  status: PostStatus;
  title: string;
  name: string;
  thumb: string;
  tile: string;
  focus: FocusState;
  modified: string;
  species: string;
  species_ids: number[];
  breed: string;
  color: string;
  age: string;
  gender: string;
  teo_id: string;
  internal_id: string;
  record_type: RecordType;
  missing_status: MissingStatus | "";
  mediator: string;
  adoption_status: string;
  successfully_adopted: boolean;
  owner_feedback_date: string;
  owner_pending: boolean;
}

export interface AnimalTdw {
  enabled: boolean;
  year: number;
  week: number;
  text: string;
  article_image: AnimalImage | null;
}

export interface AnimalInterest {
  seed: number;
  form: number;
  total: number;
}

export interface AnimalRecord {
  id: number;
  status: PostStatus;
  title: string;
  modified: string;
  permalink: string;
  preview_url: string;
  legacy_edit_url: string;
  internal_id: string;
  featured_image: AnimalImage | null;
  images: AnimalImage[];
  can_delete: boolean;
  name: string;
  teo_id: string;
  description: string;
  birthday_status: string;
  birthday: string;
  age: string;
  life_stage: string;
  color: number | null;
  size_cm: string;
  gender: string;
  castrated: string;
  vaccinated: string;
  chipped: string;
  breeds: number[];
  companions: Companion[];
  record_type: RecordType;
  mediator: string;
  adoption_status: string;
  participates_in_nori: boolean;
  missing_status: MissingStatus;
  missing: Record<string, string>;
  private_contact: Record<string, string>;
  adoption_requirements: string[];
  internal_adoption_status: string;
  owner_pending_action: string;
  adoption_date: string;
  owner_feedback_date: string;
  adoption_source: string;
  adoption_source_other: string;
  show_in_frontend: boolean;
  owner_photos: AnimalImage[];
  owner_text: string;
  tdw: AnimalTdw;
  interest: AnimalInterest | null;
}
