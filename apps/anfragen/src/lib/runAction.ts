import { describeError } from "@web-app-kit/frontend";
import { toast } from "@web-app-kit/frontend/ui";
import type { ActionResult } from "@/types";

const ACTION_ERROR = "Das hat nicht geklappt. Bitte versuche es erneut.";

export async function runAction(
  action: () => Promise<ActionResult>,
  onDone?: () => void | Promise<void>,
): Promise<boolean> {
  try {
    const result = await action();
    toast.success(result.message);
    await onDone?.();
    return true;
  } catch (error) {
    toast.error(describeError(error, ACTION_ERROR));
    return false;
  }
}
