import path from "node:path";
import { noForeignUi } from "./no-foreign-ui.js";
import { createRuleTester } from "./rule-tester.helper.js";

function toolFile(relativePath) {
  return path.join(process.cwd(), relativePath);
}

function foreign(source) {
  return { messageId: "foreignImport", data: { source } };
}

function icons(source) {
  return { messageId: "foreignIcons", data: { source } };
}

function localCopy(source) {
  return { messageId: "localCopyImport", data: { source } };
}

createRuleTester().run("no-foreign-ui (invalid)", noForeignUi, {
  valid: [],
  invalid: [
    {
      name: "radix-ui and @radix-ui packages",
      code: `import { Dialog } from "radix-ui";
        import * as Tabs from "@radix-ui/react-tabs";`,
      errors: [foreign("radix-ui"), foreign("@radix-ui/react-tabs")],
    },
    {
      name: "icon packages other than Tabler React",
      code: `import { Plus } from "lucide-react";
        import { FaBeer } from "react-icons/fa";
        import { XMarkIcon } from "@heroicons/react/24/solid";
        import { IconPlus } from "@tabler/icons";`,
      errors: [
        icons("lucide-react"),
        icons("react-icons/fa"),
        icons("@heroicons/react/24/solid"),
        icons("@tabler/icons"),
      ],
    },
    {
      name: "local shadcn copies via alias and relative path",
      code: `import { Tabs } from "@/components/ui/tabs";
        import { Skeleton } from "../components/ui/skeleton";
        import * as Ui from "~/components/ui";`,
      errors: [
        localCopy("@/components/ui/tabs"),
        localCopy("../components/ui/skeleton"),
        localCopy("~/components/ui"),
      ],
    },
    {
      name: "shadcn package imports other than the stylesheet",
      code: `import { cn } from "shadcn/utils";
        import "shadcn";`,
      errors: [foreign("shadcn/utils"), foreign("shadcn")],
    },
    {
      name: "re-exports and dynamic imports",
      code: `export { Slot } from "radix-ui";
        export * from "@/components/ui/button";
        const lazy = () => import("lucide-react");`,
      errors: [foreign("radix-ui"), localCopy("@/components/ui/button"), icons("lucide-react")],
    },
    {
      name: "file under src/components/ui is a local copy",
      code: 'import { cva } from "class-variance-authority";',
      filename: toolFile("src/components/ui/button.tsx"),
      errors: [{ messageId: "localCopyFile" }],
    },
    {
      name: "file under components/ui without src",
      code: 'import { Switch as SwitchPrimitive } from "radix-ui";',
      filename: toolFile("components/ui/switch.tsx"),
      errors: [{ messageId: "localCopyFile" }, foreign("radix-ui")],
    },
    {
      name: "allow option does not exempt other sources",
      code: `import { LineChart } from "recharts";
        import { Plus } from "lucide-react";`,
      options: [{ allow: ["recharts"] }],
      errors: [icons("lucide-react")],
    },
    {
      name: "allow entry is not a plain string prefix",
      code: 'import { Tabs } from "@radix-ui/react-tabs";',
      options: [{ allow: ["@radix-ui/react-tab"] }],
      errors: [foreign("@radix-ui/react-tabs")],
    },
  ],
});
