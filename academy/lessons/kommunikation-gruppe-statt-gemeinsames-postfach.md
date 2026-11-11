---
title: "kommunikation@: Gruppe statt gemeinsames Postfach"
topics: [admin, anwender, google-workspace, mails]
audience: admin
updated: 2026-11-10
---
kommunikation@tierschutzverein-duesseldorf.de ist eine **Google-Gruppe** und kein gemeinsames Postfach. Diese Seite erklärt, warum das so ist, wie die Mails ankommen und wie Kolleginnen und Kollegen damit arbeiten.

## Warum eine Gruppe und kein gemeinsames Postfach

kommunikation@ wurde als Gruppe angelegt, bevor feststand, wie gemeinsame Postfächer in Google Workspace am besten funktionieren. Heute gilt: Neue gemeinsame Postfächer sind Nutzerkonten mit Delegierung, Gruppen tragen Adressen nach dem Muster team-<bereich>@. Details stehen unter „Gemeinsames Postfach einrichten (Nutzerkonto + Delegierung)“.

Ein Umbau von kommunikation@ zum Nutzerkonto wurde am 10.11.2026 versucht und rückgängig gemacht. Google legt für jede Adresse automatisch eine Testdomain-Adresse an (…test-google-a.com), über die die Kopien aus All-Inkl ankommen. Nach dem Umbenennen blieb die alte Testdomain-Adresse an der Gruppe hängen und ließ sich nicht löschen, nach dem Zurückbenennen war sie ganz weg. Ein Nutzerkonto kommunikation@ hätte damit keine eigene Testdomain-Adresse bekommen. Deshalb bleibt kommunikation@ eine Gruppe.

## Wie die Mails ankommen

- Mails aus dem Verein (Vereinskonten) stellt Google direkt an die Gruppe zu.
- Mails von außen kommen zuerst bei All-Inkl an. All-Inkl schickt eine Kopie an `team-kommunikation@tierschutzverein-duesseldorf.de.test-google-a.com`. Diese Adresse gehört zur Gruppe.
- Die Gruppe verteilt jede Mail an alle Mitglieder.

**Wichtig für Admins:** Die Gruppe trägt die Aliasse `team-kommunikation@` und `team-kommunikation@…test-google-a.com`. Sie stammen aus dem Umbau-Versuch. Lösche sie nicht und benenne die Gruppe nicht erneut um: Der Kopieempfänger in All-Inkl hängt an der Testdomain-Adresse. Geht sie verloren, weist Google jede Kopie von außen ab.

## So arbeitest Du mit kommunikation@

1. Ein Admin nimmt Dich als Mitglied in die Gruppe auf. Ab dann bekommst Du die Mails an kommunikation@ in Deinen eigenen Posteingang.
2. Damit Du als kommunikation@ antworten kannst, trägst Du die Adresse einmal in Gmail unter „Senden als“ ein. Wie das geht, steht unter „Als Gruppe senden und Gruppen-Mails sortieren“.
3. Mit einem Filter und einem Label hältst Du die Mails der Gruppe getrennt von Deinen eigenen. Auch das steht in derselben Anleitung.

Anders als im gemeinsamen Postfach sieht jede Person die Mails in ihrem eigenen Posteingang. Ob jemand eine Mail schon beantwortet hat, seht Ihr dort nicht. Stimmt Euch deshalb ab, wer welche Mails beantwortet, zum Beispiel per Chat oder indem Ihr die Antwort in Kopie an kommunikation@ schickt.

## Testen nach Änderungen

- Eine Mail von einer privaten Adresse an kommunikation@ muss bei allen Mitgliedern ankommen, auch im Spam-Ordner nachsehen.
- Eine Antwort als kommunikation@ an eine externe Adresse muss ankommen.
- Kommt eine Fehlermeldung „550 5.1.1 … does not exist“ zurück, stimmt das Ziel des Kopieempfängers in All-Inkl nicht mehr mit der Testdomain-Adresse der Gruppe überein.

## Quellen

- [Google Workspace-Hilfe: Dual Delivery und Testdomain-Adressen](https://knowledge.workspace.google.com/admin/gmail/advanced/deliver-email-to-multiple-inboxes-with-dual-delivery?hl=de)
- [Google-Groups-Community: Aliasse nach dem Umbenennen einer Gruppe](https://support.google.com/groups/thread/357785338)
