export const PATHS = {
  home: "/",
  colors: "/grundlagen/farben",
  radii: "/grundlagen/radien",
  components: "/komponenten",
  status: "/status",
} as const;

export const PREVIEW_PREFIX = "/vorschau/";

export function componentPath(slug: string): string {
  return `${PATHS.components}/${slug}`;
}

export function componentSlugOf(path: string): string | null {
  const prefix = `${PATHS.components}/`;
  if (!path.startsWith(prefix)) return null;
  return path.slice(prefix.length) || null;
}

export function previewPath(previewId: string, theme: "light" | "dark"): string {
  return `${PREVIEW_PREFIX}${previewId}?theme=${theme}`;
}
