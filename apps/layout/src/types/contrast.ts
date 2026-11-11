export type Rgb = [number, number, number];

export interface ContrastIssue {
  sectionId: string;
  cellId: string;
  elementId: string;
  label: string;
  ratio: number;
  required: number;
  isOnPhoto: boolean;
}
