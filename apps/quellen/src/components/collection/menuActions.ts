import type { Taxonomy, Term } from "@/types";
import type { MenuAction, OpenTaxonomyDialog } from "./collection.types";

function deleteAction(onSelect: () => void): MenuAction {
  return { label: "Löschen", isDestructive: true, onSelect };
}

export function taxonomyActions(taxonomy: Taxonomy, onOpen: OpenTaxonomyDialog): MenuAction[] {
  return [
    {
      label: "Begriff hinzufügen",
      onSelect: () => onOpen({ type: "term-create", taxonomy, parentId: null }),
    },
    { label: "Bearbeiten", onSelect: () => onOpen({ type: "taxonomy-edit", taxonomy }) },
    deleteAction(() => onOpen({ type: "taxonomy-delete", taxonomy })),
  ];
}

export function termActions(taxonomy: Taxonomy, term: Term, onOpen: OpenTaxonomyDialog) {
  const addChild: MenuAction = {
    label: "Unterbegriff hinzufügen",
    onSelect: () => onOpen({ type: "term-create", taxonomy, parentId: term.id }),
  };
  return [
    ...(taxonomy.is_hierarchical ? [addChild] : []),
    { label: "Bearbeiten", onSelect: () => onOpen({ type: "term-edit", taxonomy, term }) },
    deleteAction(() => onOpen({ type: "term-delete", term })),
  ];
}
