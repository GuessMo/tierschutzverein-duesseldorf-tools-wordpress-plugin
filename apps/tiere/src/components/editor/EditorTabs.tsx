import { useState, type ReactNode } from "react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@web-app-kit/frontend/ui";
import type { EditorTabsProps } from "./editor.types";
import {
  CompanionsSection,
  DetailsSection,
  InterestSection,
  MediaSection,
  ProfileSection,
  StatusSection,
  StorySection,
  TdwSection,
} from "./sections";

interface EditorTab {
  value: string;
  label: string;
  content: ReactNode;
}

function editorTabs({ section, media, record }: EditorTabsProps): EditorTab[] {
  const isAdoption = section.draft.record_type === "vermittlung";
  const tabs: EditorTab[] = [
    { value: "profile", label: "Steckbrief", content: <ProfileSection {...section} /> },
    { value: "media", label: "Bilder", content: <MediaSection {...media} /> },
    { value: "details", label: "Gesundheit & Alter", content: <DetailsSection {...section} /> },
    {
      value: "status",
      label: isAdoption ? "Vermittlung" : "Meldung",
      content: (
        <>
          <StatusSection {...section} />
          <CompanionsSection {...section} />
        </>
      ),
    },
  ];
  if (!isAdoption) return tabs;
  return [
    ...tabs,
    { value: "story", label: "Nach der Vermittlung", content: <StorySection {...media} /> },
    {
      value: "website",
      label: "Website",
      content: (
        <>
          <TdwSection {...media} />
          <InterestSection {...section} record={record} />
        </>
      ),
    },
  ];
}

export function EditorTabs(props: EditorTabsProps) {
  const tabs = editorTabs(props);
  const [active, setActive] = useState("profile");
  const current = tabs.some((tab) => tab.value === active) ? active : "profile";
  return (
    <Tabs value={current} onValueChange={setActive} className="gap-6">
      <div className="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        <TabsList variant="line" aria-label="Bereiche des Tiers">
          {tabs.map((tab) => (
            <TabsTrigger key={tab.value} value={tab.value}>
              {tab.label}
            </TabsTrigger>
          ))}
        </TabsList>
      </div>
      {tabs.map((tab) => (
        <TabsContent key={tab.value} value={tab.value} className="flex flex-col gap-8">
          {tab.content}
        </TabsContent>
      ))}
    </Tabs>
  );
}
