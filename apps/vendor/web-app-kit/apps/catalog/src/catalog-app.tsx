import { useEffect } from "react";
import { resolvePage } from "./pages/resolve-page";
import { interceptLinkClicks } from "./routing/link-interception";
import { usePath } from "./routing/use-path";
import { CatalogShell } from "./shell/catalog-shell";

const SITE_TITLE = "Kit-Katalog";

export function CatalogApp() {
  const path = usePath();
  const page = resolvePage(path);
  useEffect(() => interceptLinkClicks(), []);
  useEffect(() => {
    document.title = `${page.title} · ${SITE_TITLE}`;
  }, [page.title]);
  return <CatalogShell path={path}>{page.content}</CatalogShell>;
}
