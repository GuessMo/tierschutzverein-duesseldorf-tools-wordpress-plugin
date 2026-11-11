import path from "node:path";
import { noForeignUi } from "./no-foreign-ui.js";
import { createRuleTester } from "./rule-tester.helper.js";

function toolFile(relativePath) {
  return path.join(process.cwd(), relativePath);
}

createRuleTester().run("no-foreign-ui (valid)", noForeignUi, {
  valid: [
    {
      name: "kit building blocks and Tabler icons",
      code: `import { Button, cn } from "@web-app-kit/frontend/ui";
        import { IconPlus } from "@tabler/icons-react";`,
    },
    {
      name: "own app element outside components/ui",
      code: 'import { ProjectCard } from "@/components/app/project-card";',
      filename: toolFile("src/components/app/project-card.tsx"),
    },
    {
      name: "other folders that only contain ui in their name",
      code: 'import { Toolbar } from "../components/uikit/toolbar";',
      filename: toolFile("src/features/ui/toolbar.tsx"),
    },
    {
      name: "shadcn tailwind stylesheet",
      code: 'import "shadcn/tailwind.css";',
    },
    {
      name: "npm package path that is not a local copy",
      code: 'import { x } from "some-lib/components/ui/button";',
    },
    {
      name: "allow option exempts an import source and its sub paths",
      code: `import { Chart } from "@radix-ui/react-chart";
        import { Bar } from "@radix-ui/react-chart/bar";`,
      options: [{ allow: ["@radix-ui/react-chart"] }],
    },
    {
      name: "allow option exempts a local copy by file path and import path",
      code: 'import { ResponsiveContainer } from "recharts";',
      filename: toolFile("src/components/ui/chart.tsx"),
      options: [{ allow: ["src/components/ui/chart.tsx"] }],
    },
    {
      name: "allow option with a directory prefix",
      code: 'import { ChartContainer } from "@/components/ui/chart";',
      filename: toolFile("src/components/ui/legacy/table.tsx"),
      options: [{ allow: ["src/components/ui/legacy/", "@/components/ui/chart"] }],
    },
  ],
  invalid: [],
});
