import { createContext, useContext } from "react";

export const HelpTexts = createContext<Record<string, string>>({});

export function useHelpText(fieldId: string): string | undefined {
  return useContext(HelpTexts)[fieldId.replace(/-/g, "_")];
}
