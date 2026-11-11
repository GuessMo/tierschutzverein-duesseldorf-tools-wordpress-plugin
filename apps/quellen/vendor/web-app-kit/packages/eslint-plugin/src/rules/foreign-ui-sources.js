const RADIX_PACKAGES = ["radix-ui", "@radix-ui"];

const ICON_PACKAGES = [
  "lucide",
  "lucide-react",
  "@lucide",
  "react-icons",
  "react-feather",
  "@heroicons",
  "@phosphor-icons",
  "phosphor-react",
  "@fortawesome",
  "@mui/icons-material",
  "@iconify",
  "@mdi",
  "@remixicon",
  "@ant-design/icons",
  "@primer/octicons-react",
  "@tabler/icons",
];

const SHADCN_PACKAGE = "shadcn";
const ALLOWED_SHADCN_SOURCES = ["shadcn/tailwind.css"];
const LOCAL_SOURCE_PREFIXES = [".", "/", "@/", "~/"];
const LOCAL_COPY_PATTERN = /(^|\/)components\/ui(\/|$)/;
const LOCAL_COPY_FILE_PATTERN = /(^|\/)components\/ui\//;

export function matchesEntry(value, entry) {
  const prefix = entry.endsWith("/") ? entry : `${entry}/`;
  return value === entry || value.startsWith(prefix);
}

function matchesAny(value, entries) {
  return entries.some((entry) => matchesEntry(value, entry));
}

function isLocalCopySource(source) {
  const isLocal = LOCAL_SOURCE_PREFIXES.some((prefix) => source.startsWith(prefix));
  return isLocal && LOCAL_COPY_PATTERN.test(source);
}

function isForeignShadcnSource(source) {
  return matchesEntry(source, SHADCN_PACKAGE) && !ALLOWED_SHADCN_SOURCES.includes(source);
}

export function classifySource(source) {
  if (matchesAny(source, ICON_PACKAGES)) return "foreignIcons";
  if (matchesAny(source, RADIX_PACKAGES)) return "foreignImport";
  if (isForeignShadcnSource(source)) return "foreignImport";
  if (isLocalCopySource(source)) return "localCopyImport";
  return null;
}

export function isLocalCopyFile(relativePath) {
  return LOCAL_COPY_FILE_PATTERN.test(relativePath);
}

export function isAllowed(value, allow) {
  return matchesAny(value, allow);
}
