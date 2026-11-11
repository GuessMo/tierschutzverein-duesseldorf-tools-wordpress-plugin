const CODE_CLASSES =
  "overflow-x-auto rounded-md border bg-card px-4 py-3 font-mono text-sm leading-relaxed";

export function CodeBlock({ code, label }: { code: string; label: string }) {
  return (
    <pre aria-label={label} className={CODE_CLASSES}>
      <code>{code}</code>
    </pre>
  );
}
