import type { CatalogEntry } from "./catalog.types";
import { UI_IMPORT_PATH } from "./groups";

const MAX_INLINE_LENGTH = 72;

function formatImport(keyword: string, names: string[], path: string): string {
  const inline = `${keyword} { ${names.join(", ")} } from "${path}";`;
  if (inline.length <= MAX_INLINE_LENGTH) return inline;
  const lines = names.map((name) => `  ${name},`).join("\n");
  return `${keyword} {\n${lines}\n} from "${path}";`;
}

export function importStatement({
  exports,
  types,
  importPath = UI_IMPORT_PATH,
}: Pick<CatalogEntry, "exports" | "types" | "importPath">) {
  const statements = [formatImport("import", exports, importPath)];
  if (types?.length) statements.push(formatImport("import type", types, importPath));
  return statements.join("\n");
}
