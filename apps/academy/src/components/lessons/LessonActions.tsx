import { useState } from "react";
import { IconArrowLeft, IconFileDownload } from "@tabler/icons-react";
import { Button, toast } from "@web-app-kit/frontend/ui";
import { topicHash } from "@/hooks";
import { downloadLessonPdf } from "@/lib";
import type { Lesson } from "@/types";

export function LessonActions({ lesson }: { lesson: Lesson }) {
  const [isSaving, setIsSaving] = useState(false);
  const save = async () => {
    setIsSaving(true);
    try {
      await downloadLessonPdf(lesson);
    } catch {
      toast.error("Das PDF konnte nicht erstellt werden. Bitte versuche es noch einmal.");
    } finally {
      setIsSaving(false);
    }
  };
  return (
    <div className="flex flex-wrap items-center gap-2">
      <Button asChild variant="ghost" size="sm">
        <a href={topicHash(lesson.topics[0]?.slug ?? "")}>
          <IconArrowLeft data-icon="inline-start" />
          Zur Übersicht
        </a>
      </Button>
      <Button variant="outline" size="sm" onClick={() => void save()} disabled={isSaving}>
        <IconFileDownload data-icon="inline-start" />
        {isSaving ? "PDF wird erstellt …" : "Als PDF speichern"}
      </Button>
    </div>
  );
}
