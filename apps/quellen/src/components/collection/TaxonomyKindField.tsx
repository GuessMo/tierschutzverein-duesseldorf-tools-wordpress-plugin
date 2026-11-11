import { Label, ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import type { TaxonomyKindFieldProps } from "./collection.types";

export function TaxonomyKindField(props: TaxonomyKindFieldProps) {
  const { isHierarchical, wasHierarchical, onChange } = props;
  return (
    <div className="flex flex-col gap-1.5">
      <Label id="taxonomy-kind-label">Art</Label>
      <ToggleGroup
        type="single"
        variant="outline"
        aria-labelledby="taxonomy-kind-label"
        value={isHierarchical ? "tree" : "tags"}
        onValueChange={(value) => value && onChange(value === "tree")}
      >
        <ToggleGroupItem value="tree">Mit Unterbegriffen</ToggleGroupItem>
        <ToggleGroupItem value="tags">Schlagwörter</ToggleGroupItem>
      </ToggleGroup>
      {wasHierarchical && !isHierarchical && (
        <p className="text-xs text-muted-foreground">
          Beim Speichern rücken alle Unterbegriffe auf die oberste Ebene.
        </p>
      )}
    </div>
  );
}
