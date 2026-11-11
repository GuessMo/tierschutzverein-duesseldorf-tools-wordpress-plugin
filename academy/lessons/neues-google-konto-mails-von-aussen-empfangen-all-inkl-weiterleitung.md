---
title: "Neues Google-Konto: Mails von außen empfangen (All-Inkl-Weiterleitung)"
topics: [admin, google-workspace, mails]
audience: admin
updated: 2026-11-10
---
Legst Du ein neues Google-Workspace-Konto an, kommen Mails von außen erst an, wenn es in All-Inkl eine Weiterleitung zu Google gibt. Das gilt für persönliche Konten (z. B. tina.hof@) genauso wie für gemeinsame Postfächer (z. B. kontakt@) und für jeden Alias. Wie Du ein gemeinsames Postfach einrichtest, steht unter „Gemeinsames Postfach einrichten“.

## Warum die Weiterleitung nötig ist

- Der MX-Eintrag der Domain zeigt auf All-Inkl (w01046a2.kasserver.com). Externe Absender liefern deshalb jede Mail an @tierschutzverein-duesseldorf.de zuerst bei All-Inkl ab, nicht bei Google.
- Kennt All-Inkl die Adresse nicht, lehnt der Server die Mail ab. Der Absender bekommt dann eine Fehlermeldung wie „Ihre E-Mail konnte nicht zugestellt werden“.
- Mails aus dem Verein selbst (Kolleginnen und Kollegen, Website-Formulare über web@) stellt Google intern zu. Deshalb wirkt ein neues Konto zunächst fertig, obwohl externe Mails noch fehlen.
- Adressen, die nur in All-Inkl existieren und kein Google-Konto haben, bleiben unverändert. Für sie ist nichts zu tun.

## Weiterleitung anlegen

1. Zuerst das Google-Konto anlegen. Ohne Konto bei Google läuft die Weiterleitung ins Leere.
2. Im All-Inkl-Verwaltungsbereich (KAS) unter „E-Mail“ eine neue E-Mail-Weiterleitung einrichten. All-Inkl beschreibt das in der Anleitung „E-Mail-Weiterleitung einrichten“.
3. Als Adresse den Teil vor dem @ des Google-Kontos eintragen, z. B. `tina.hof`, Domain tierschutzverein-duesseldorf.de.
4. Als Ziel dieselbe Adresse mit der Google-Testdomain eintragen: `tina.hof@tierschutzverein-duesseldorf.de.test-google-a.com`. Diese Zusatzadresse hat jedes Google-Konto automatisch.
5. Speichern.

**Wichtig:** Nie die normale @tierschutzverein-duesseldorf.de-Adresse als Ziel eintragen. Die Mail landet sonst wieder bei All-Inkl und läuft im Kreis. Diese Weiterleitung ist für Adressen gedacht, die bei All-Inkl noch kein Postfach haben. Hat die Adresse dort schon ein Postfach, bleibt es bestehen: Dann trägt die Person im Webmailer einen Kopieempfänger ein, siehe „Weiterleitung von All-Inkl zu Deinem Google-Konto einrichten“. Den bisherigen Bestand überträgt der Admin per Datenimport, siehe „Google-Workspace-Konto für eine Kollegin oder einen Kollegen anlegen“, Schritt 7.

## Testen

1. Von einer privaten Adresse außerhalb des Vereins (z. B. GMX oder web.de) eine Mail an die neue Adresse senden.
2. Im Gmail des Kontos nachsehen, auch im Spam-Ordner. Weitergeleitete Mails landen anfangs teilweise dort. Dann die Mail öffnen und „Kein Spam“ wählen.
3. Kommt nichts an, etwas warten und erneut testen. Die Weiterleitung greift erfahrungsgemäß manchmal erst nach einiger Zeit, auch wenn All-Inkl die Adresse schon annimmt.
4. Kommt eine Fehlermeldung zurück, in KAS Adresse und Ziel auf Tippfehler prüfen.

## Später: Umstellung auf Google

Solange viele Adressen nur in All-Inkl existieren, bleibt der MX-Eintrag bei All-Inkl. Nutzen später alle Google Workspace, kann der MX-Eintrag auf Google umgestellt werden. Danach sind die Weiterleitungen überflüssig. Vorher müssen alle Postfächer, die noch bei All-Inkl gelesen werden, zu Google umgezogen sein.

## Offener Punkt: SPF-Eintrag

Stand 01.10.2026 enthält der TXT-Eintrag der Domain zwei SPF-Regeln (All-Inkl und Google) in einem Eintrag. Das macht SPF ungültig und kann die Zustellung von Vereinsmails verschlechtern. Zusammenlegen zu: `v=spf1 a mx include:spf.kasserver.com include:_spf.google.com ~all`

## Quellen

- [Google Workspace-Hilfe: Weiterleitung an den Testdomain-Alias (dual delivery)](https://knowledge.workspace.google.com/admin/gmail/advanced/deliver-email-to-multiple-inboxes-with-dual-delivery)
- [All-Inkl-Hilfe: E-Mail-Weiterleitung einrichten](https://all-inkl.com/suche/e-mail)
