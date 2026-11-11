import { useEffect, useRef } from "react";
import { IconBold, IconItalic, IconLink, IconList, IconListNumbers } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";

interface RichTextFieldProps {
  id: string;
  label: string;
  html: string;
  onChange: (html: string) => void;
}

const COMMANDS = [
  { command: "bold", label: "Fett", icon: IconBold },
  { command: "italic", label: "Kursiv", icon: IconItalic },
  { command: "insertUnorderedList", label: "Aufzählung", icon: IconList },
  { command: "insertOrderedList", label: "Nummerierte Liste", icon: IconListNumbers },
] as const;

const EDITOR_CLASSES = [
  "nl-richtext min-h-32 rounded-b-lg border border-t-0 bg-background px-3 py-2 text-sm",
  "outline-none focus-visible:ring-2 focus-visible:ring-ring",
].join(" ");

export function RichTextField({ id, label, html, onChange }: RichTextFieldProps) {
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    if (ref.current && ref.current.innerHTML !== html) ref.current.innerHTML = html;
  }, [html]);
  const run = (command: string, value?: string) => {
    ref.current?.focus();
    document.execCommand(command, false, value);
    onChange(ref.current?.innerHTML ?? "");
  };
  const addLink = () => {
    const url = window.prompt("Adresse des Links (https://…)");
    if (url) run("createLink", url);
  };
  return (
    <div className="flex flex-col">
      <div
        role="toolbar"
        aria-label={`${label}: Formatierung`}
        className="flex gap-1 rounded-t-lg border bg-muted/40 p-1"
      >
        {COMMANDS.map(({ command, label: name, icon: Icon }) => (
          <Button
            key={command}
            type="button"
            variant="ghost"
            size="icon-sm"
            aria-label={name}
            title={name}
            onClick={() => run(command)}
          >
            <Icon aria-hidden />
          </Button>
        ))}
        <Button
          type="button"
          variant="ghost"
          size="icon-sm"
          aria-label="Link"
          title="Link"
          onClick={addLink}
        >
          <IconLink aria-hidden />
        </Button>
      </div>
      <div
        ref={ref}
        id={id}
        role="textbox"
        aria-multiline="true"
        aria-label={label}
        tabIndex={0}
        contentEditable
        suppressContentEditableWarning
        className={EDITOR_CLASSES}
        onInput={() => onChange(ref.current?.innerHTML ?? "")}
      />
    </div>
  );
}
