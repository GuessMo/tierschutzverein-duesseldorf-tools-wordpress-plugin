import { cn, INTERACTIVE_SURFACE_CLASSES, SidePanel } from "@web-app-kit/frontend/ui";
import { NAV_SECTIONS } from "./nav-items";

const LINK_CLASSES = cn(
  INTERACTIVE_SURFACE_CLASSES,
  "flex min-h-8 w-full items-center rounded-md px-2 py-1 text-sm",
);

export function CatalogNav({ path }: { path: string }) {
  return (
    <SidePanel side="left" width="sm">
      <div className="flex flex-col gap-6">
        {NAV_SECTIONS.map((section) => (
          <div key={section.id} className="flex flex-col gap-1">
            <p id={`nav-${section.id}`} className="px-2 text-xs font-medium text-muted-foreground">
              {section.label}
            </p>
            <ul aria-labelledby={`nav-${section.id}`} className="flex flex-col gap-0.5">
              {section.links.map((link) => (
                <li key={link.href}>
                  <a
                    href={link.href}
                    aria-current={link.href === path ? "page" : undefined}
                    className={LINK_CLASSES}
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
    </SidePanel>
  );
}
