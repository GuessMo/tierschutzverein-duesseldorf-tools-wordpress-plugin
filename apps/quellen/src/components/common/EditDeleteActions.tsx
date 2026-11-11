import { IconPencil, IconTrash } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { EditDeleteActionsProps } from "./common.types";

export function EditDeleteActions({ name, onEdit, onDelete }: EditDeleteActionsProps) {
  return (
    <>
      <Button variant="ghost" size="icon-sm" aria-label={`${name} bearbeiten`} onClick={onEdit}>
        <IconPencil />
      </Button>
      <Button variant="ghost" size="icon-sm" aria-label={`${name} löschen`} onClick={onDelete}>
        <IconTrash />
      </Button>
    </>
  );
}
