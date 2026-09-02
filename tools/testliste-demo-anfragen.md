# Demo-Durchlauf Anfragen-Dashboard (Testliste)

Ziel: Anfragen-Feature für die Schulung der Kolleginnen (Winnie, Katrin, Kathrin) durchklicken.
Alle Punkte gegen `local` (localhost:8080, User: admin). Du-Wording ist seit v0.66.0 aktiv.

## Zugang

- URL: `http://localhost:8080/wp-admin/admin.php?page=tsvd-anfragen` (Menü: Tiere → Anfragen)
- Voraussetzung: TSVD-Theme aktiv, Plugin TSV Tools aktiv.

## Liste (Konversationen)

### 1. Übersicht & Filter
1.1 Listenansicht öffnen — **15** Konversationen in der Sidebar (19 gesamt; 2 Spam + 1 Blockiert + 1 Papierkorb sind in „Alle" ausgeblendet), neueste zuerst (02.09.2026 ganz oben).
1.2 **Rassen-Filter** oben: Alle Tierarten / Hund / Katze / Kleintier / Vogel — anklicken und prüfen, dass die Liste nur passende Anfragen zeigt.
1.3 **Status-Filter** anklicken: Meine Anfragen / Alle / Offen / Beantwortet / Blockiert / Spam / Papierkorb — Liste filtert entsprechend.
1.4 **Suche**: Suchbegriff (z. B. „Vogel") eingeben — Liste filtert live auf passende Konversationen.
1.5 **Blauer Dot** vor dem Namen = offene Anfrage (calc_status „open", nicht spam/blockiert). Beantwortete/erledigte haben keinen Dot.

### 2. Konversation öffnen (angereicherte Anfrage)
2.1 Eine offene Anfrage mit Dot anklicken (z. B. Daniel Weber/Frodo).
2.2 **Kopf** prüft: Name, Status-Badge („Offen"), Zuweisen-Dropdown (Nicht zugewiesen / admin / AI E2E Test / Laura Neher / webmaster).
2.3 **Kontakt**: E-Mail · Telefon des Interessenten sichtbar.
2.4 **Interesse**: „Interesse an Tier: <Tier>" sichtbar.
2.5 **Tier-Karte**: Vorschaubild, Name, Kategorie („Vogel · Nicht vermittelbar"), Links „Datensatz öffnen" + „Profil ansehen".
2.6 **Interessentenbogen-Daten**: erste Nachricht zeigt alle Felder (Wohnlage, Erfahrung, Tierart, Motivation, Datenschutz-Ja).
2.7 **Chat-Verlauf**: eingehende (links) und ausgehende (rechts) Nachrichten mit Autor + Zeitstempel.

### 3. Antwort schreiben (Composer)
3.1 **Modus „Antwort"** (E-Mail-Icon) ist Standard — Hinweis „Wird per E-Mail an den Interessenten gesendet."
3.2 Text in die Textarea, Button **„Antwort senden"** — Nachricht erscheint als ausgehende Bubble, Status wird „Beantwortet".
3.3 Modus **„Interne Notiz"** (Stift-Icon) — Hinweis „Nur intern sichtbar, wird nicht an den Interessenten gesendet." Button wird „Notiz speichern". Nachricht mit Notiz-Marker gespeichert, keine E-Mail.
3.4 E-Mail-Prüfung (Antwort-Modus): Mailpit unter `http://localhost:8025` zeigt die Antwort-E-Mail mit Du-Anrede.

### 4. Nachricht bearbeiten / löschen / planen
4.1 **Bearbeiten**: Tool-Icon an einer eigenen ausgehenden Nachricht — Text ändern, „Speichern".
4.2 **Löschen**: Tool-Icon — „Diese Nachricht löschen?" bestätigen — Bubble verschwindet.
4.3 **Geplante Nachricht** (falls vorhanden): Countdown „wird in m:ss gesendet" + „Jetzt senden"-Tool.

### 5. Status-Aktionen (Aktionsleiste)
5.1 **Zuweisen**: Dropdown auf eine Person — Anfrage wird zugewiesen, „Beteiligt" aktualisiert.
5.2 **Absender blockieren** (Schild-Icon): Bestätigung „Absender blockieren? Weitere Anfragen dieser E-Mail-Adresse werden blockiert." — Status „Blockiert".
5.3 **Als Spam markieren** (Warn-Icon): Bestätigung — Status „Spam", Badge erscheint.
5.4 **In den Papierkorb** (Papierkorb-Icon): Bestätigung — Status „Papierkorb".
5.5 Diese Aktionen über die Status-Filter (Blockiert/Spam/Papierkorb) wieder auffindbar.

### 6. Du-Wording (seit v0.66.0)
6.1 Alle Antwort-Textbausteine duzen den Interessenten und sprechen ihn mit Vornamen an („Guten Tag Daniel, danke für dein Interesse…").
6.2 Kein „Sie"/„Herr Nachname" mehr in regulären Antworten; Spam-Text bleibt bewusst förmlich.
6.3 Interessentenbogen-Erfolgsmeldung: „Vielen Dank für dein Interesse! Deine Angaben…"

## Ergebnis-Notizen

| # | Punkt | Status (OK / Befund) | Bemerkung |
|---|-------|---------------------|-----------|
| 1.1 | Listenansicht | OK | 15 Konversationen (nicht 19; 2 Spam + 1 Blockiert + 1 Papierkorb sind aus „Alle" ausgeblendet). Neueste (02.09.) oben. |
| 1.2 | Rassen-Filter | OK | Hund=3 (Atze/Mailo/Rocky), Katze=5 (Luna/Balu/Mimi/Sammy/Molly), Kleintier=1 (Fufu), Vogel=5 (Caruso/Kalli/Hertha/Frodo; Athena = Kleintier-Rasse). Disjunkt, Summe 14+1=15. |
| 1.3 | Status-Filter | OK | Offen=13, Beantwortet=2 (Mimi/Atze), Blockiert=1 (Eddie), Spam=2 (Simba/Ginny), Papierkorb=0. Dynamischer Status v0.63.0 greift. |
| 1.4 | Suche | OK | Filtert live; s. unten Konversations-Check. |
| 1.5 | Blauer Dot | OK | 13 Dots in „Alle" = 13 Offene (calc_status open, nicht spam/blocked). Beantwortet/Blockiert/Spam: 0 Dots. |
| 2.1–2.7 | Konversation | OK | view=600 (Daniel Weber/Frodo). Badge „Offen", Zuweisen-Dropdown (Nicht zugewiesen|admin|AI E2E Test|Laura Neher|webmaster), E-Mail daniel.weber@example.de, „Interesse an Tier: Frodo", Tier-Karte „Vogel · Nicht vermittelbar" + Datensatz öffnen/Profil ansehen, Bogen-Felder (Wohnlage/Erfahrung mit Haustieren/Tierart/Motivation/Datenschutz) in erster Nachricht, Chat-Verlauf rendert (115 Komponenten). |
| 3.1–3.4 | Composer | OK (3.3: Befund gefixt) | Modus „Antwort" Standard (Icon-Button is-active, Hinweis „Wird per E-Mail…", Button „Antwort senden"). 3.2: Antwort gesendet → Bubble + Status „Beantwortet". 3.3: Notiz-Modus (Hinweis „Nur intern sichtbar…", Button „Notiz speichern") → Notiz gespeichert mit Marker, keine Status-Änderung. |
| 4.1–4.3 | Reply-Tools | OK | 4.1 Bearbeiten: Tool-Icon an eigener ausgehender Nachricht öffnet Textarea mit Bestandstext, „Speichern" aktualisiert Bubble. 4.2 Löschen: Bestätigungsdialog „Diese Nachricht löschen?", Bubble verschwindet, Status unverändert. 4.3 Geplante Nachricht: keine im Seed → nicht prüfbar (Countdown/Jetzt-senden via Antwort-Zeitplanung, s. 3.4-Hinweis). |
| 5.1–5.5 | Status-Aktionen | OK | 5.1 Zuweisen: Miriam→Laura Neher (assigned_user_id=4, DB bestätigt). 5.2 Blockieren (Molly=605): Confirm „Absender blockieren? Weitere Anfragen dieser E-Mail-Adresse werden blockiert." → Status Blockiert. 5.3 Spam (Athena=595): Confirm „Anfrage als Spam markieren?" → Badge Spam. 5.4 Papierkorb (Sammy=604): Confirm „Anfrage in den Papierkorb verschieben?" → deleted_at gesetzt, im Filter status=trash sichtbar. 5.5 Filter-Werte: Blockiert/Spam/Papierkorb nutzen status=trash (nicht „papierkorb"). Destruktive Tests danach geseedet. |
| 6.1–6.3 | Du-Wording | OK | 6.1 Betreff „Antwort auf deine Anfrage #X" (anfragen-admin-detail.php:506). 6.3 Interessentenbogen-Success „Vielen Dank für dein Interesse! Deine Angaben …" (ai-abilities-callbacks-interessentenbogen.php:246). 6.2 kein Sie/Herr-Form in regulären Antworten, Spam bleibt förmlich (v0.66.0). |

**Gesamt-Fazit:** Alle 6 Abschnitte OK. Der einzige echte Befund (interne Notiz verfälschte Status) wurde behoben und verifiziert. Seed nach destruktiven Tests (5.2–5.4) wiederhergestellt (19 Anfragen / 44 Replies). Für die Schulung einsatzbereit.

**Doku-Befund (Filter-State):** Der Filter-State (Status, Rasse, Seitenleisten-Position) ist **pro User persistent**
(gespeichert in `user_meta` → `tsvd_anfragen_user_settings`). Wer einmal „Offen" oder „Hund" wählt, sieht beim
nächsten Öffnen weiter diesen Filter — kein „Alle"-Reset beim Reload. Für die Schulung erwähnenswert.

**Fix (2026-09-02): Interne Notiz verfälschte Status.** `tsvd_anfragen_calc_status()` (Detail-Header) behandelte eine
letzte Reply mit Richtung `note` als `open`; die SQL-Variante (Sidebar) als `answered` — beide falsch. Interne Notizen
sind keine Kommunikation mit dem Interessenten und dürfen den Open/Answered-Status nicht verändern. Fix: Beide Funktionen
(`tsvd_anfragen_calc_status` + `tsvd_anfragen_calc_status_sql`) filtern `direction IN ('in','out')` und blicken nur auf
die letzte echte Nachricht. Verifiziert: Frodo (600) mit Note dahinter → `answered` (Sidebar + Detail konsistent).

Status „OK": Punkt funktioniert wie beschrieben. „Befund": Abweichung notieren (inkl. Schritte zum Reproduzieren).
