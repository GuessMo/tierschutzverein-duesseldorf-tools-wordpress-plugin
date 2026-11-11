import { useState } from "react";
import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { academyApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { AppControls } from "@/components/controls";
import { LessonList, LessonView, TopicNav } from "@/components/lessons";
import { topicHash, useAcademyRoute, useResource } from "@/hooks";
import type { AcademyIndex, AcademyRoute } from "@/types";

function Content({
  index,
  route,
  search,
}: {
  index: AcademyIndex;
  route: AcademyRoute;
  search: string;
}) {
  if (route.view === "lesson") return <LessonView key={route.slug} slug={route.slug} />;
  return <LessonList index={index} topic={route.topic} search={search} />;
}

export function App() {
  const index = useResource(academyApi.index);
  const route = useAcademyRoute();
  const [search, setSearch] = useState("");
  const onSearch = (value: string) => {
    setSearch(value);
    if (route.view === "lesson") window.location.hash = topicHash("");
  };
  const activeTopic = route.view === "list" ? route.topic : "";
  const navigation = index.data && (
    <TopicNav
      topics={index.data.topics}
      lessons={index.data.lessons}
      activeTopic={activeTopic}
      search={search}
      onSearch={onSearch}
    />
  );
  return (
    <AppShell
      brand="Academy"
      homeHref={topicHash("")}
      skipLinkLabel="Zum Inhalt springen"
      controls={<AppControls />}
      navigation={navigation ?? null}
      navigationLabel="Themen"
    >
      <PageContainer size="lg">
        {index.error && <ErrorMessage message={index.error} onRetry={() => void index.reload()} />}
        {!index.data && !index.error && <LoadingRows label="Academy wird geladen" count={6} />}
        {index.data && <Content index={index.data} route={route} search={search} />}
      </PageContainer>
    </AppShell>
  );
}
