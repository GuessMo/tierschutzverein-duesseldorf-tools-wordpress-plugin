---
title: "Gemeinsamen Kalender einrichten (Outlook-Kalender übernehmen)"
topics: [admin, google-workspace]
audience: admin
updated: 2026-10-12
---
Diese Anleitung beschreibt, wie Du einen bestehenden Outlook-Kalender in den Google Kalender eines gemeinsamen Postfachs übernimmst, zum Beispiel **tina.hof@**, und ihn für die Kolleginnen und Kollegen freigibst. Die Anleitung für die Kolleginnen und Kollegen steht unter „Gemeinsamen Kalender nutzen“.

**Wichtig:** Die Delegierung des Postfachs umfasst den Kalender nicht. Wer Zugriff auf das Postfach hat, sieht den Kalender erst, wenn Du ihn zusätzlich freigibst.

## Bevor Du anfängst

- Das gemeinsame Postfach existiert als Nutzerkonto, siehe „Gemeinsames Postfach einrichten“. Passwort und Bestätigung in zwei Schritten liegen beim Admin.
- Du brauchst Zugriff auf den Outlook-Kalender am Computer, im klassischen Outlook oder im neuen Outlook für Windows. Der Export läuft je nach Version unterschiedlich, siehe Schritt 1.
- Der Import ist eine **einmalige Kopie**. Änderungen, die danach noch in Outlook gemacht werden, kommen in Google nicht an. Leg deshalb einen Stichtag fest, ab dem nur noch der Google Kalender gilt.
- **Gäste und Videokonferenz-Daten werden nicht übernommen.** Titel, Zeit, Ort und Beschreibung bleiben erhalten.
- In der Admin-Konsole ist für die Freigabe innerhalb des Vereins nichts einzustellen. Die interne Freigabeoption dort ist nur eine Voreinstellung. Einzelne Personen gibt der Kontoinhaber selbst frei.

## 1. Outlook-Kalender exportieren

Das klassische Outlook erkennst Du am Menü „Datei“ oben links. Das neue Outlook für Windows hat dieses Menü nicht.

### Klassisches Outlook

1. Öffne in Outlook den Kalender und wähle den Kalender aus, den Du übernehmen willst.
2. Klicke auf „Datei“ → „Kalender speichern“.
3. Behalte den vorgeschlagenen Dateinamen oder gib einen eigenen ein.
4. Klicke auf „Weitere Optionen“. Wähle bei „Zeitraum“ den vollständigen Kalender und bei „Detail“ die Stufe mit allen Details.
5. Klicke auf „OK“ und dann auf „Speichern“. Du erhältst eine Datei mit der Endung `.ics`.

Ein großer Zeitraum kann eine große Datei ergeben. Das Speichern dauert dann etwas länger.

### Neues Outlook für Windows

Hier gibt es kein „Kalender speichern“. Stattdessen veröffentlichst Du den Kalender kurz und lädst ihn über den ICS-Link als Datei herunter.

1. Wähle links im Navigationsbereich „Kalender“.
2. Klicke im Menüband auf die Registerkarte „Ansicht“ → „Kalendereinstellungen“ → „Kalender“ → „Freigegebene Kalender“.
3. Wähle im Abschnitt „Kalender veröffentlichen“ den Kalender aus der Liste und die Berechtigung mit allen Details.
4. Klicke auf „Veröffentlichen“. Darunter erscheinen ein HTML-Link und ein ICS-Link.
5. Kopiere den **ICS-Link**, füge ihn in die Adresszeile des Browsers ein und speichere die Datei. Sie muss auf `.ics` enden.
6. Klicke danach auf „Veröffentlichung aufheben“. Solange der Kalender veröffentlicht ist, kann jeder mit dem Link ihn ansehen.

Steht der Kalender nicht in der Liste oder fehlt der Abschnitt „Kalender veröffentlichen“, nutze das klassische Outlook oder frag die Person, der der Kalender gehört. Füge den ICS-Link nicht über „Per URL“ in Google ein. Dann wäre es nur ein Abo des Outlook-Kalenders und kein eigener Kalender des Postfachs.

## 2. Als Postfach-Konto anmelden

1. Öffne ein privates Browserfenster. So bleibt Dein eigenes Konto im normalen Fenster angemeldet.
2. Öffne [calendar.google.com](https://calendar.google.com) und melde Dich als das gemeinsame Postfach an, zum Beispiel tina.hof@. Passwort und zweiten Schritt findest Du im Passwortmanager.

## 3. Termine importieren

1. Klicke rechts oben auf das Zahnrad → „Einstellungen“.
2. Klicke links auf „Importieren & Exportieren“.
3. Klicke auf „Datei von meinem Computer auswählen“ und wähle die `.ics`-Datei aus Schritt 1.
4. Wähle bei „Zu Kalender hinzufügen“ den Hauptkalender des Postfachs. Er trägt den Namen des Kontos.
5. Klicke auf „Importieren“. Importiere die Datei nur einmal.
6. Prüfe danach einige Termine, besonders Serientermine und ganztägige Termine.

## 4. Kalender für die Kolleginnen und Kollegen freigeben

1. Klicke links auf „Meine Kalender“.
2. Klicke rechts neben dem Kalender des Postfachs auf das Dreipunkt-Menü → „Einstellungen und Freigabe“.
3. Klicke links auf „Geteilt mit“ und dann auf „Personen und Gruppen hinzufügen“.
4. Trage die Vereinsadressen der Kolleginnen und Kollegen ein.
5. Wähle die Berechtigung (siehe unten) und klicke auf „Senden“.

Die Kolleginnen und Kollegen bekommen eine Mail. Erst wenn sie auf den Link darin klicken, erscheint der Kalender bei ihnen.

### Welche Berechtigung?

- **„Änderungen vornehmen und Termindetails ansehen“:** Termine anlegen, ändern und löschen. Für die tägliche Arbeit reicht das.
- **„Änderungen vornehmen und Freigabe verwalten“:** volle Kontrolle, auch über die Freigabe. Die Person kann den Kalender dann selbst für weitere Personen freigeben. Gib diese Stufe nur, wenn das gewollt ist.

## 5. Outlook-Kalender stilllegen

- Sag allen, die den Outlook-Kalender genutzt haben, ab welchem Tag nur noch der Google Kalender gilt.
- Trage danach keine Termine mehr in Outlook ein. Sonst laufen zwei Kalender auseinander.
- Lösche den Outlook-Kalender erst, wenn alle mit dem Google Kalender arbeiten.

## Einladungen von außen

Einladungen kommen per Mail an die Adresse des Postfachs. Mails von außen erreichen ein Google-Konto nur über die Weiterleitung in All-Inkl. Prüfe das wie in „Neues Google-Konto: Mails von außen empfangen“ beschrieben.

## Offboarding

Scheidet jemand aus: als Postfach-Konto anmelden → „Meine Kalender“ → Dreipunkt-Menü am Kalender → „Einstellungen und Freigabe“ → unter „Geteilt mit“ neben der Person auf „Entfernen“. Denk auch an die Delegierung des Postfachs, siehe „Gemeinsames Postfach einrichten“.

## Quellen

- [Microsoft-Hilfe: Exportieren eines Outlook-Kalenders nach Google Kalender (klassisches Outlook)](https://support.microsoft.com/de-de/outlook/export-an-outlook-calendar-to-google-calendar)
- [Microsoft-Hilfe: Freigeben eines Outlook-Kalenders, Abschnitt „Kalender veröffentlichen“ (neues Outlook für Windows)](https://support.microsoft.com/de-de/outlook/sharing/share-an-outlook-calendar-as-view-only-with-others)
- [Google Kalender-Hilfe: Termine in Google Kalender importieren](https://support.google.com/calendar/answer/37118?hl=de&co=GENIE.Platform%3DDesktop)
- [Google Kalender-Hilfe: Kalender freigeben](https://support.google.com/calendar/answer/37082?hl=de)
- [Google Workspace-Hilfe: Freigabeoptionen für Google Kalender festlegen](https://support.google.com/a/answer/60765?hl=de)
