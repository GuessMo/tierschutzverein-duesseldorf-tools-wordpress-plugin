import { IconLanguage } from "@tabler/icons-react";
import { Button } from "./button.js";
import { cn } from "./cn.js";
import type { LanguageOption, LanguageToggleProps } from "./language-toggle.types.js";

function includeVisibleCode(description: string, visibleCode: string): string {
  const words = description.toUpperCase().split(/[^\p{L}\p{N}-]+/u);
  if (words.includes(visibleCode)) return description;
  return `${visibleCode} ${description}`;
}

function findCurrentIndex(languages: LanguageOption[], value: string): number {
  return Math.max(
    languages.findIndex((language) => language.code === value),
    0,
  );
}

export function LanguageToggle({
  languages,
  value,
  onChange,
  describe,
  className,
  ...props
}: LanguageToggleProps) {
  if (languages.length === 0) return null;
  const index = findCurrentIndex(languages, value);
  const current = languages[index];
  const next = languages[(index + 1) % languages.length];
  const visibleCode = current.code.toUpperCase();
  const label = includeVisibleCode(describe(current), visibleCode);
  return (
    <Button
      type="button"
      variant="ghost"
      size="default"
      aria-label={label}
      title={label}
      onClick={() => onChange(next.code)}
      className={cn("px-2 text-xs", className)}
      {...props}
    >
      <IconLanguage />
      <span>{visibleCode}</span>
    </Button>
  );
}
