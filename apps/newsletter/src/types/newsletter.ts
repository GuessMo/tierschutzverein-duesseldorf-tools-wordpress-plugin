export type BlockKind = "rte" | "image" | "website-updates" | "academy-updates";

export interface BlockData {
  html?: string;
  id?: number;
  alt?: string;
  url?: string;
}

export interface NewsletterBlock {
  id: string;
  type: BlockKind;
  data: BlockData;
}

export interface IssueSummary {
  id: number;
  title: string;
  sentAt: string;
  modified: string;
  blockCount: number;
}

export interface IssueDetail extends IssueSummary {
  blocks: NewsletterBlock[];
}

export interface BlockType {
  type: BlockKind;
  label: string;
  dynamic: boolean;
  description: string;
  status: string;
  manageUrl: string;
}

export interface IssueIndex {
  issues: IssueSummary[];
  subscriberCount: number;
  blockTypes: BlockType[];
}

export interface Subscriber {
  id: number;
  name: string;
  email: string;
  isSubscribed: boolean;
}

export interface SendResult {
  mode: "test" | "all";
  sent: number;
  total: number;
  failed: number;
  issue: IssueSummary;
}

export interface UploadedImage {
  id: number;
  url: string;
}

export type NewsletterRoute =
  | { view: "issues" }
  | { view: "issue"; id: number }
  | { view: "subscribers" }
  | { view: "blocks" };
