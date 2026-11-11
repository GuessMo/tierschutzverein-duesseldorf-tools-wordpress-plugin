export type RoleKind = "text" | "line";

export interface ColorRole {
  name: string;
  className: string;
  kind: RoleKind;
  minimum: number | null;
}

export interface ColorRoleGroup {
  id: string;
  title: string;
  roles: ColorRole[];
}

export interface RenderedColor {
  value: string;
  ratio: number | null;
}

export interface ScaleItem {
  name: string;
  className: string;
}
