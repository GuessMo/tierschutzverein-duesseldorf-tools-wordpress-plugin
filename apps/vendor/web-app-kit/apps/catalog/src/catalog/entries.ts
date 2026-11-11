import type { CatalogEntry } from "./catalog.types";
import { accountMenuEntry } from "./components/account-menu";
import { alertDialogEntry } from "./components/alert-dialog";
import { appLauncherEntry } from "./components/app-launcher";
import { appShellEntry } from "./components/app-shell";
import { appVersionEntry } from "./components/app-version";
import { badgeEntry } from "./components/badge";
import { buttonEntry } from "./components/button";
import { cardEntry } from "./components/card";
import { checkboxEntry } from "./components/checkbox";
import { commandEntry } from "./components/command";
import { contextMenuEntry } from "./components/context-menu";
import { dialogEntry } from "./components/dialog";
import { dropdownMenuEntry } from "./components/dropdown-menu";
import { inputEntry } from "./components/input";
import { inputGroupEntry } from "./components/input-group";
import { labelEntry } from "./components/label";
import { multiComboboxEntry } from "./components/multi-combobox";
import { languageToggleEntry } from "./components/language-toggle";
import { metaRowEntry } from "./components/meta-row";
import { pageContainerEntry } from "./components/page-container";
import { popoverEntry } from "./components/popover";
import { progressEntry } from "./components/progress";
import { selectEntry } from "./components/select";
import { separatorEntry } from "./components/separator";
import { sidePanelEntry } from "./components/side-panel";
import { skeletonEntry } from "./components/skeleton";
import { switchEntry } from "./components/switch";
import { tableEntry } from "./components/table";
import { tabsEntry } from "./components/tabs";
import { textareaEntry } from "./components/textarea";
import { themeToggleEntry } from "./components/theme-toggle";
import { toastEntry } from "./components/toast";
import { toggleEntry } from "./components/toggle";
import { toggleGroupEntry } from "./components/toggle-group";
import { tooltipEntry } from "./components/tooltip";

export const CATALOG_ENTRIES: CatalogEntry[] = [
  appShellEntry,
  sidePanelEntry,
  pageContainerEntry,
  appVersionEntry,
  themeToggleEntry,
  languageToggleEntry,
  buttonEntry,
  badgeEntry,
  metaRowEntry,
  cardEntry,
  tableEntry,
  progressEntry,
  skeletonEntry,
  tooltipEntry,
  inputEntry,
  inputGroupEntry,
  textareaEntry,
  labelEntry,
  checkboxEntry,
  switchEntry,
  selectEntry,
  multiComboboxEntry,
  separatorEntry,
  tabsEntry,
  dropdownMenuEntry,
  appLauncherEntry,
  accountMenuEntry,
  contextMenuEntry,
  commandEntry,
  dialogEntry,
  alertDialogEntry,
  popoverEntry,
  toastEntry,
  toggleEntry,
  toggleGroupEntry,
];

export function findEntry(slug: string): CatalogEntry | undefined {
  return CATALOG_ENTRIES.find((entry) => entry.slug === slug);
}
