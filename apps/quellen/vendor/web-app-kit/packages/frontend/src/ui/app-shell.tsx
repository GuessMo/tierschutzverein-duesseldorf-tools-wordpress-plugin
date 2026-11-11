import type {
  AppShellBodyProps,
  AppShellBrandProps,
  AppShellHeaderProps,
  AppShellLinkProps,
  AppShellProps,
} from "./app-shell.types.js";
import { APP_SHELL_CONTENT_ID } from "./app-shell.constants.js";
import { AppShellPanel } from "./app-shell-panel.js";
import { cn } from "./cn.js";
import { INTERACTIVE_SURFACE_CLASSES } from "./interactive-surface.js";

const SKIP_LINK_CLASSES = [
  "sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50",
  "focus:rounded-md focus:bg-background focus:px-3 focus:py-2 focus:text-sm",
  "focus:ring-3 focus:ring-ring transition-[translate]",
  "hover:overlay-hover active:translate-y-px",
].join(" ");

const FOOTER_CLASSES = [
  "flex h-8 shrink-0 items-center gap-2 border-t bg-background px-4",
  "text-xs text-muted-foreground",
].join(" ");

const BRAND_CLASSES = [
  "-ml-2 flex min-w-0 shrink items-center gap-2 overflow-hidden whitespace-nowrap",
  "rounded-md px-2 py-1 text-sm font-semibold",
].join(" ");

function renderPlainLink(props: AppShellLinkProps) {
  return <a {...props} />;
}

function AppShellBrand({ brand, homeHref, renderLink = renderPlainLink }: AppShellBrandProps) {
  if (!homeHref) {
    return (
      <span data-slot="app-shell-brand" className={BRAND_CLASSES}>
        {brand}
      </span>
    );
  }
  return renderLink({
    href: homeHref,
    className: cn(INTERACTIVE_SURFACE_CLASSES, BRAND_CLASSES),
    "data-slot": "app-shell-brand",
    children: brand,
  });
}

function AppShellHeader({ brand, homeHref, renderLink, actions, controls }: AppShellHeaderProps) {
  return (
    <header className="flex h-14 shrink-0 items-center gap-2 border-b bg-background px-4 sm:gap-4">
      <AppShellBrand brand={brand} homeHref={homeHref} renderLink={renderLink} />
      <div className="-mx-1 flex min-w-0 flex-1 items-center gap-2 overflow-hidden p-1">
        {actions}
      </div>
      <div className="flex shrink-0 items-center gap-1">{controls}</div>
    </header>
  );
}

function AppShellBody({
  navigation,
  navigationLabel,
  detail,
  detailLabel,
  children,
}: AppShellBodyProps) {
  return (
    <div className="flex min-h-0 flex-1 flex-col md:flex-row">
      {navigation && (
        <AppShellPanel as="nav" label={navigationLabel ?? ""} position="start">
          {navigation}
        </AppShellPanel>
      )}
      <main
        id={APP_SHELL_CONTENT_ID}
        tabIndex={-1}
        className="min-h-0 min-w-0 flex-1 overflow-auto outline-none"
      >
        {children}
      </main>
      {detail && (
        <AppShellPanel as="aside" label={detailLabel ?? ""} position="end">
          {detail}
        </AppShellPanel>
      )}
    </div>
  );
}

export function AppShell({
  brand,
  homeHref,
  renderLink,
  actions,
  controls,
  skipLinkLabel,
  footer,
  ...body
}: AppShellProps) {
  return (
    <div data-slot="app-shell" className="flex h-dvh flex-col bg-background text-foreground">
      <a href={`#${APP_SHELL_CONTENT_ID}`} className={SKIP_LINK_CLASSES}>
        {skipLinkLabel}
      </a>
      <AppShellHeader
        brand={brand}
        homeHref={homeHref}
        renderLink={renderLink}
        actions={actions}
        controls={controls}
      />
      <AppShellBody {...body} />
      {footer && (
        <footer data-slot="app-shell-footer" className={FOOTER_CLASSES}>
          {footer}
        </footer>
      )}
    </div>
  );
}
