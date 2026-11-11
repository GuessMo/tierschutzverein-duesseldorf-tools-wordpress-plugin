import { useEffect, useState } from "react";
import { IconSearch } from "@tabler/icons-react";
import { InputGroup, InputGroupAddon, InputGroupInput } from "@web-app-kit/frontend/ui";
import type { SearchFieldProps } from "./list.types";

const DEBOUNCE_MS = 300;

export function SearchField({ value, onChange }: SearchFieldProps) {
  const [text, setText] = useState(value);
  useEffect(() => {
    if (text === value) return;
    const timer = window.setTimeout(() => onChange(text), DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [text, value, onChange]);
  return (
    <InputGroup>
      <InputGroupAddon>
        <IconSearch aria-hidden />
      </InputGroupAddon>
      <InputGroupInput
        type="search"
        aria-label="Anfragen durchsuchen"
        placeholder="Name, E-Mail, Tier"
        value={text}
        onChange={(event) => setText(event.target.value)}
      />
    </InputGroup>
  );
}
