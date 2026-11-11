import type { ComponentType } from "react";
import { ColorRoles } from "../basics/color-roles";
import { NeutralScale } from "../basics/neutral-scale";
import { RadiusScale } from "../basics/radius-scale";
import { CATALOG_ENTRIES } from "../catalog/entries";
import { BASICS_PREVIEW_IDS, componentPreviewId } from "./preview-ids";

const BASICS_PREVIEWS: Record<string, ComponentType> = {
  [BASICS_PREVIEW_IDS.colorRoles]: ColorRoles,
  [BASICS_PREVIEW_IDS.neutrals]: NeutralScale,
  [BASICS_PREVIEW_IDS.radii]: RadiusScale,
};

const COMPONENT_PREVIEWS: Record<string, ComponentType> = Object.fromEntries(
  CATALOG_ENTRIES.flatMap((entry) =>
    entry.variants.map((variant) => [componentPreviewId(entry.slug, variant.key), variant.Preview]),
  ),
);

export function resolvePreview(previewId: string): ComponentType | null {
  return BASICS_PREVIEWS[previewId] ?? COMPONENT_PREVIEWS[previewId] ?? null;
}
