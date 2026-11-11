import path from "node:path";
import {
  classifySource,
  isAllowed,
  isLocalCopyFile,
} from "./foreign-ui-sources.js";

const KIT_HINT =
  "Kit-Baustein aus @web-app-kit/frontend/ui nutzen; fehlt er, im Kit ergänzen " +
  "(Skill web-app-kit-component).";

const MESSAGES = {
  foreignImport: `Import aus "{{source}}" ist im Tool nicht erlaubt. ${KIT_HINT}`,
  foreignIcons:
    `Icon-Paket "{{source}}" ist im Tool nicht erlaubt, Icons nur aus @tabler/icons-react. ` +
    KIT_HINT,
  localCopyImport: `Import einer lokalen UI-Kopie "{{source}}". ${KIT_HINT}`,
  localCopyFile:
    `Dateien unter components/ui/ sind lokale Kopien von UI-Bausteinen. ${KIT_HINT}`,
};

function toRelativePath(context) {
  const relativePath = path.relative(context.cwd, context.filename);
  return relativePath.split(path.sep).join("/");
}

function reportSource(context, node, allow) {
  if (node?.type !== "Literal" || typeof node.value !== "string") return;
  if (isAllowed(node.value, allow)) return;
  const messageId = classifySource(node.value);
  if (!messageId) return;
  context.report({ node, messageId, data: { source: node.value } });
}

function reportLocalCopyFile(context, node, allow) {
  const relativePath = toRelativePath(context);
  if (!isLocalCopyFile(relativePath)) return;
  if (isAllowed(relativePath, allow)) return;
  context.report({ node, messageId: "localCopyFile" });
}

export const noForeignUi = {
  meta: {
    type: "problem",
    docs: {
      description:
        "Tools use kit building blocks instead of foreign UI packages or local shadcn copies.",
    },
    schema: [
      {
        type: "object",
        properties: { allow: { type: "array", items: { type: "string" } } },
        additionalProperties: false,
      },
    ],
    defaultOptions: [{ allow: [] }],
    messages: MESSAGES,
  },
  create(context) {
    const [{ allow }] = context.options;
    const checkSource = (node) => reportSource(context, node.source, allow);
    return {
      Program: (node) => reportLocalCopyFile(context, node, allow),
      ImportDeclaration: checkSource,
      ExportNamedDeclaration: checkSource,
      ExportAllDeclaration: checkSource,
      ImportExpression: checkSource,
    };
  },
};
