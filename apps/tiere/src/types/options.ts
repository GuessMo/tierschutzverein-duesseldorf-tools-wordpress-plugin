export interface ValueLabel {
  value: string;
  label: string;
}

export type Tone = "success" | "warning" | "danger" | "info";

export interface StatusOption extends ValueLabel {
  text: string;
  tone: Tone;
}

export interface TermOption {
  id: number;
  name: string;
  slug: string;
}

export interface SpeciesOption extends TermOption {
  breeds: TermOption[];
}

export interface MissingFieldOption {
  key: string;
  label: string;
  type: "date" | "text";
  states: string[];
  readonly_states: string[];
}

export interface RequirementSet {
  label: string;
  options: ValueLabel[];
}

export type OptionSetKey = "life_stage" | "gender" | "birthday_status" | "ternary" | "size";

export interface AnimalCaps {
  publish: boolean;
  upload: boolean;
  approve: boolean;
}

export interface AnimalOptions {
  species: SpeciesOption[];
  colors: TermOption[];
  option_sets: Record<OptionSetKey, ValueLabel[]>;
  adoption_statuses: StatusOption[];
  missing_statuses: StatusOption[];
  missing_fields: MissingFieldOption[];
  requirement_sets: Record<string, RequirementSet>;
  adoption_sources: ValueLabel[];
  caps: AnimalCaps;
}
