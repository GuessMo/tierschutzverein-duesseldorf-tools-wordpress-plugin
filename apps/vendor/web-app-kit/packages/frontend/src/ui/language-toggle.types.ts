import type { ComponentProps } from "react";
import type { Button } from "./button.js";

export interface LanguageOption {
  code: string;
  label: string;
}

export interface LanguageToggleProps extends Omit<
  ComponentProps<typeof Button>,
  "children" | "onClick" | "onChange" | "value"
> {
  languages: LanguageOption[];
  value: string;
  onChange: (code: string) => void;
  describe: (language: LanguageOption) => string;
}
