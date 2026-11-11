export const BASICS_PREVIEW_IDS = {
  colorRoles: "grundlagen/farbrollen",
  neutrals: "grundlagen/grautoene",
  radii: "grundlagen/radien",
} as const;

export function componentPreviewId(slug: string, variantKey: string): string {
  return `komponenten/${slug}/${variantKey}`;
}
