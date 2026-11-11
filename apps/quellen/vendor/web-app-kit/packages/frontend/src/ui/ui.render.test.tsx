import assert from "node:assert/strict";
import { test } from "node:test";
import { renderToString } from "react-dom/server";
import { ThemeProvider } from "../ThemeProvider.js";
import {
  Badge,
  Button,
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  Input,
  Label,
  Table,
  TableBody,
  TableCell,
  TableRow,
  Textarea,
  ThemeToggle,
  cn,
} from "./index.js";

const LABELS = { system: "System", light: "Hell", dark: "Dunkel" };

function installBrowserStubs() {
  const store = new Map<string, string>();
  Object.assign(globalThis, {
    window: { matchMedia: () => ({ matches: false }), document: {} },
    localStorage: {
      getItem: (key: string) => store.get(key) ?? null,
      setItem: (key: string, value: string) => store.set(key, value),
    },
  });
}

function renderPrimitives() {
  return renderToString(
    <Card>
      <CardHeader>
        <CardTitle>Title</CardTitle>
      </CardHeader>
      <CardContent>
        <Label htmlFor="name">Name</Label>
        <Input id="name" />
        <Textarea />
        <Badge variant="secondary">New</Badge>
        <Button variant="outline" size="sm">
          Save
        </Button>
        <Table>
          <TableBody>
            <TableRow>
              <TableCell>Cell</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>,
  );
}

function renderThemeToggle() {
  return renderToString(
    <ThemeProvider>
      <ThemeToggle labels={LABELS} describe={(label) => `Theme: ${label}`} />
    </ThemeProvider>,
  );
}

installBrowserStubs();

test("cn merges conflicting tailwind classes", () => {
  assert.equal(cn("px-2", "px-4"), "px-4");
});

test("primitives render with their data-slot markers", () => {
  const html = renderPrimitives();
  for (const slot of ["card", "button", "input", "textarea", "badge", "table-cell"]) {
    assert.match(html, new RegExp(`data-slot="${slot}"`), slot);
  }
});

test("ThemeToggle uses the app-provided description and the system icon", () => {
  const html = renderThemeToggle();
  assert.match(html, /aria-label="Theme: System"/);
  assert.match(html, /tabler-icon-device-desktop/);
});

const BADGE_VARIANTS = ["default", "secondary", "destructive", "outline", "ghost", "link"] as const;

function renderLinkBadgeClasses(variant: (typeof BADGE_VARIANTS)[number]) {
  const html = renderToString(
    <Badge variant={variant} asChild>
      <a href="/x">Link</a>
    </Badge>,
  );
  assert.match(html, /^<a /, variant);
  return /class="([^"]*)"/.exec(html)?.[1].split(" ") ?? [];
}

test("link badges carry hover, pressed and focus-visible states in every variant", () => {
  for (const variant of BADGE_VARIANTS) {
    const classes = renderLinkBadgeClasses(variant);
    assert.ok(classes.includes("[a]:active:translate-y-px"), `${variant}: pressed`);
    assert.ok(classes.includes("focus-visible:ring-[3px]"), `${variant}: focus-visible`);
    assert.ok(classes.some((token) => token.includes("hover:")), `${variant}: hover`);
  }
});
