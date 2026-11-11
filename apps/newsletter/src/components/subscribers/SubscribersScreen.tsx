import { useEffect, useMemo, useState } from "react";
import { IconSearch } from "@tabler/icons-react";
import {
  Button,
  InputGroup,
  InputGroupAddon,
  InputGroupInput,
  PageContainer,
  toast,
} from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { useResource } from "@/hooks";
import { SubscriberList } from "./SubscriberList";

export function SubscribersScreen() {
  const people = useResource(newsletterApi.subscribers);
  const [selected, setSelected] = useState<Set<number>>(new Set());
  const [search, setSearch] = useState("");
  const [isSaving, setIsSaving] = useState(false);
  useEffect(() => {
    if (people.data)
      setSelected(new Set(people.data.filter((p) => p.isSubscribed).map((p) => p.id)));
  }, [people.data]);
  const visible = useMemo(() => {
    const needle = search.trim().toLowerCase();
    return (people.data ?? []).filter((p) => `${p.name} ${p.email}`.toLowerCase().includes(needle));
  }, [people.data, search]);
  const toggle = (id: number, isOn: boolean) =>
    setSelected((current) => {
      const next = new Set(current);
      if (isOn) next.add(id);
      else next.delete(id);
      return next;
    });
  const save = async () => {
    setIsSaving(true);
    try {
      const result = await newsletterApi.saveSubscribers([...selected]);
      toast.success(`Gespeichert: ${result.count} Abonnenten.`);
    } catch {
      toast.error("Die Abonnenten konnten nicht gespeichert werden.");
    } finally {
      setIsSaving(false);
    }
  };
  return (
    <PageContainer size="md" className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div className="flex flex-col gap-1">
          <h1 className="text-2xl font-semibold">Abonnenten</h1>
          <p className="text-sm text-muted-foreground">
            Der Newsletter geht nur an die hier angehakten Kolleginnen und Kollegen.
          </p>
        </div>
        <Button onClick={() => void save()} disabled={isSaving || !people.data}>
          {isSaving ? "Wird gespeichert …" : `Speichern (${selected.size})`}
        </Button>
      </div>
      <InputGroup>
        <InputGroupAddon>
          <IconSearch aria-hidden />
        </InputGroupAddon>
        <InputGroupInput
          type="search"
          aria-label="Personen durchsuchen"
          placeholder="Name oder E-Mail"
          value={search}
          onChange={(event) => setSearch(event.target.value)}
        />
      </InputGroup>
      {people.error && <ErrorMessage message={people.error} onRetry={() => void people.reload()} />}
      {!people.data && !people.error && <LoadingRows label="Personen werden geladen" />}
      {people.data && <SubscriberList people={visible} selected={selected} onToggle={toggle} />}
    </PageContainer>
  );
}
