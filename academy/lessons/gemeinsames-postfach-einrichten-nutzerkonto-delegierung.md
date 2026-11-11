---
title: "Gemeinsames Postfach einrichten (Nutzerkonto + Delegierung)"
topics: [admin, google-workspace, mails]
audience: admin
updated: 2026-11-10
---
Diese Anleitung beschreibt, wie Du ein gemeinsames Postfach wie **kontakt@** als Google-Workspace-Nutzerkonto mit Delegierung einrichtest. Das Passwort bleibt beim Admin. Kolleginnen und Kollegen greifen über ihr eigenes Konto zu. Die Anleitung für die Kolleginnen und Kollegen steht unter „Gemeinsame Postfächer nutzen“.

## Gemeinsames Postfach oder Gruppe?

Im Verein gibt es zwei Bausteine mit unterschiedlichen Aufgaben. Sie ersetzen sich nicht gegenseitig.

- **Gemeinsames Postfach = Nutzerkonto + Delegierung.** Für Funktionsadressen, an die Menschen von außen schreiben, z. B. kontakt@, kommunikation@, tieranfragen@. Ein Posteingang mit gemeinsamem Gelesen-Status, alle antworten direkt als die Adresse. Kostet eine Lizenz, die kostenlose Nonprofit-Lizenz reicht.
- **Gruppe = Personenkreis für Freigaben.** Für Drive-Ordner, Kalender und später Rechte auf der Website. Statt jede Person einzeln freizugeben, gibst Du der Gruppe frei. Keine Lizenz. Mails an die Gruppe kommen als Kopie bei jedem Mitglied an, deshalb ist eine Gruppe kein gemeinsames Postfach.
- **Alias:** keine eigene Lizenz, aber auch kein eigenes Postfach. Er gehört immer zu genau einem Konto, siehe Abschnitt „Alias“ unten.

### Adressen für Gruppen

- Jede Gruppe braucht eine eigene Adresse. Gruppen heißen immer **team-<bereich>@**, z. B. team-kommunikation@, team-vermittlung@.
- Der Name ohne „team-“ bleibt für das gemeinsame Postfach frei: kommunikation@ ist das Postfach, team-kommunikation@ die Gruppe der Personen, die dort arbeiten.
- Gruppen stehen dadurch in Listen und Freigabe-Dialogen beieinander und sind nicht mit Postfächern zu verwechseln.
- Vorlage im Verein: **tieranfragen@** und **kontakt@** sind Nutzerkonten.

## Mail-Routing der Domain

Externe Mails an @tierschutzverein-duesseldorf.de kommen zuerst bei All-Inkl an, nicht bei Google. Jede Adresse des Postfachs braucht deshalb in All-Inkl eine Weiterleitung zu Google, auch jeder Alias. Wie das geht und wie Du es testest, steht unter „Neues Google-Konto: Mails von außen empfangen“.

## Einrichtung Schritt für Schritt

1. Delegierung freischalten (einmalig): Google Admin → Apps → Google Workspace → Gmail → Nutzereinstellungen → E-Mail-Delegierung → „Zulassen, dass Nutzer den Zugriff auf ihre Postfächer an andere Nutzer in der Domain delegieren können“. Dort auch die Absenderanzeige festlegen. Die Änderung kann bis zu 24 Stunden dauern.
2. Nutzerkonto anlegen, z. B. kontakt@. Passwort selbst setzen, im Passwortmanager ablegen und die Bestätigung in zwei Schritten mit einer Authenticator-App einrichten.
3. Einmal als dieses Konto in Gmail im Browser am Computer anmelden (in der Gmail-App geht das nicht): Zahnrad → Alle Einstellungen aufrufen → Tab „Konten“ (je nach Konto „Konten & Import“) → Abschnitt „Zugriff auf mein Konto gewähren“ → „Weiteres Konto hinzufügen“.
4. Vereinsadresse der Kollegin oder des Kollegen eintragen → „Nächster Schritt“ → „E-Mail senden, um Zugriff zu gewähren“. Für jede Person wiederholen.
5. In All-Inkl (KAS) die Weiterleitung für die Adresse zu Google anlegen, siehe „Neues Google-Konto: Mails von außen empfangen“.
6. Testen: eine Mail von einer privaten Adresse senden (Spam-Ordner mit prüfen) und einmal das Website-Formular abschicken.

**Wichtig:** Delegierte lassen sich nicht direkt in der Admin Console zuweisen. Die Admin Console schaltet die Funktion nur frei. Ohne Anmeldung als Postfach-Konto geht es nur per Gmail-API mit domainweiter Delegierung.

## Alias

### Was ist ein Alias?

- Eine zusätzliche Adresse („alternative E-Mail-Adresse“) an einem bestehenden Konto, z. B. info@ an kontakt@. Mails an den Alias landen im Posteingang dieses Kontos.
- Ein Alias gehört zu genau einem Konto. Er ist kein eigenes Google-Konto: keine Anmeldung, keine eigene Delegierung.
- Eine Adresse, die schon als Konto oder Gruppe existiert, kann kein Alias werden.
- Soll ein Alias von mehreren gelesen werden, lege ihn am gemeinsamen Postfach an (z. B. an kontakt@) und nicht am persönlichen Konto einer Kollegin oder eines Kollegen.

### Alias anlegen

1. Google Admin → Verzeichnis → Nutzer → Konto öffnen (z. B. kontakt@).
2. Links „Alternative E-Mail-Adressen hinzufügen“ → „Alternative E-Mail-Adresse“ → Teil vor dem @ eintragen → Speichern. Das kann bis zu 24 Stunden dauern.
3. In All-Inkl (KAS) die Weiterleitung für den Alias zu Google anlegen, siehe „Neues Google-Konto: Mails von außen empfangen“.

### Vom Alias aus senden

- Das Anlegen in der Admin Console reicht nur zum Empfangen. Zum Senden muss der Alias zusätzlich in Gmail unter „Senden als“ eingetragen werden. Das macht der Nutzer selbst, in der Admin Console ist es nicht sichtbar.
- **Alias am eigenen Konto:** Der Kontoinhaber trägt ihn ein: Zahnrad → Alle Einstellungen aufrufen → „Konten“ → „Senden als“ → „Weitere E-Mail-Adresse hinzufügen“, Haken bei „Als Alias behandeln“ gesetzt lassen.
- **Alias am gemeinsamen Postfach:** Mehrere Personen können vom Alias senden, wenn jede ihn in ihrem eigenen Gmail unter „Senden als“ einträgt, dann mit entferntem Haken bei „Als Alias behandeln“. Die Bestätigungsmail landet im gemeinsamen Postfach. Schritte für die Kolleginnen und Kollegen: Anleitung „Gemeinsame Postfächer nutzen“, Abschnitt „E-Mail-Alias“.
- Ob Bevollmächtigte einen Alias direkt im gemeinsamen Postfach als Absender wählen können, beschreibt Google nicht. Vor der Schulung einmal testen.

## Wenn die Anmeldung als Postfach-Konto nicht klappt

Die Bestätigung in zwei Schritten ist für den Verein erzwungen. Ein Konto, bei dem sie nicht eingerichtet ist (oder abgeschaltet wurde), kann sich dann nicht mehr anmelden.

1. **Zweiter Schritt eingerichtet, aber das Gerät fehlt:** Google Admin → Verzeichnis → Nutzer → Konto öffnen → Sicherheit → Bestätigung in zwei Schritten → „Back-up-Codes abrufen“. Mit einem Code anmelden.
2. **Zweiter Schritt nicht eingerichtet:** Eine Gruppe nur für dieses Konto anlegen. Google Admin → Sicherheit → Authentifizierung → Bestätigung in zwei Schritten → links die Gruppe wählen → „Nutzer dürfen die Bestätigung in zwei Schritten aktivieren“ anhaken → Erzwingung „Aus“ → Speichern. Gruppeneinstellungen haben Vorrang vor der Organisationseinheit.
3. Mit dem Passwort anmelden und unter myaccount.google.com → Sicherheit die Bestätigung in zwei Schritten mit einer Authenticator-App einrichten.
4. Das Konto wieder aus der Ausnahme-Gruppe entfernen. Google empfiehlt die Ausnahme nicht als Dauerlösung.

**Achtung bei tieranfragen@:** Wird die Bestätigung in zwei Schritten abgeschaltet, werden App-Passwörter ungültig. Der Abruf der Anfragen auf der Website funktioniert dann nicht mehr. Nach dem erneuten Einrichten ein neues App-Passwort erstellen und im WordPress-Backend unter Anfragen → Einstellungen → IMAP eintragen.

## Bestehende Gruppe zum gemeinsamen Postfach machen

**Achtung, Stand 10.11.2026:** Dieser Umbau ist bei kommunikation@ gescheitert und wurde rückgängig gemacht. Google legt für jede Adresse automatisch eine Testdomain-Adresse an (…test-google-a.com), über die die Kopien aus All-Inkl ankommen. Nach dem Umbenennen blieb die alte Testdomain-Adresse an der Gruppe hängen und ließ sich nicht löschen. Ein Nutzerkonto mit der alten Adresse bekommt dann keine eigene Testdomain-Adresse. Geh den Weg unten nur, wenn der Google-Support vorher zugesagt hat, die alte Testdomain-Adresse zu entfernen. Die Gruppe kann auch gelöscht werden, das kostet aber Archiv und Freigaben und lässt sich nicht rückgängig machen. Warum kommunikation@ eine Gruppe bleibt, steht unter „kommunikation@: Gruppe statt gemeinsames Postfach“.

1. **Adresse der Gruppe ändern, in Google Groups:** groups.google.com → Gruppe öffnen → links „Gruppeneinstellungen“ → „Allgemein“ → E-Mail-Adresse auf team-kommunikation@ ändern → Speichern. In Google Groups wird die alte Adresse **nicht** als Alias an der Gruppe behalten.
2. **Prüfen:** Google Admin → Verzeichnis → Gruppen → Gruppe öffnen → „Gruppeninformationen“ → Abschnitt „Aliasse“. Dort darf kommunikation@ nicht stehen. Wurde die Adresse in der Admin-Konsole geändert, legt Google die alte Adresse automatisch als Alias an. Diesen Alias entfernen; das kann bis zu 24 Stunden dauern, erst danach ist die Adresse frei.
3. Nutzerkonto kommunikation@ anlegen und delegieren, wie unter „Einrichtung Schritt für Schritt“ beschrieben.
4. **All-Inkl (KAS):** Gab es für die Gruppenadresse schon eine Weiterleitung zu Google, bleibt sie bestehen und gilt jetzt für das Nutzerkonto. Keine zweite Weiterleitung anlegen. Fehlt sie, anlegen wie unter „Neues Google-Konto: Mails von außen empfangen“. Hat die Adresse bei All-Inkl ein Postfach, bleibt es bestehen und bekommt einen Kopieempfänger.
5. Freigaben und Abläufe prüfen, die an kommunikation@ geschickt haben, z. B. Formulare oder Weiterleitungen. Sie erreichen jetzt das Postfach, nicht mehr alle Gruppenmitglieder.
6. Testen wie unter „Einrichtung Schritt für Schritt“.

Ältere Unterhaltungen bleiben im Archiv der Gruppe. Sie wandern nicht in das neue Postfach. Mails aus einem All-Inkl-Postfach dagegen lassen sich per Datenimport übernehmen, siehe „Google-Workspace-Konto für eine Kollegin oder einen Kollegen anlegen“, Schritt 7.

## Grenzen der Delegierung

- Nur Konten aus derselben Organisation können Bevollmächtigte sein.
- Bis zu 1.000 Bevollmächtigte pro Konto, etwa 40 gleichzeitig aktiv.
- Einladungen gelten eine Woche, der Zugriff wirkt nach bis zu 24 Stunden.
- Bevollmächtigte können kein Passwort ändern und haben keinen Zugriff auf Chat, Drive-Anhänge, Gemini oder die Kontoeinstellungen.

## Offboarding

Scheidet jemand aus, entferne die Person als Bevollmächtigte: als Postfach-Konto anmelden → Zahnrad → Alle Einstellungen aufrufen → „Konten & Import“ → Abschnitt „Zugriff auf mein Konto gewähren“ → neben der Person auf „Löschen“. Ein Passwortwechsel ist nicht nötig, weil das Passwort nie verteilt wurde.

## Quellen

- [Google Workspace-Hilfe: Gmail-Delegierung für Nutzer zulassen](https://support.google.com/a/answer/7223765)
- [Gmail-Hilfe: Zugriff auf E-Mails delegieren](https://support.google.com/mail/answer/138350?hl=de)
- [Google Workspace-Hilfe: Alternative E-Mail-Adresse (Alias) hinzufügen](https://knowledge.workspace.google.com/admin/users/add-or-delete-an-alternate-email-address-email-alias)
- [Google-Hilfe: „Als Alias behandeln“](https://support.google.com/a/answer/1710338)
- [Google Workspace-Hilfe: Bestätigung in zwei Schritten implementieren](https://support.google.com/a/answer/9176657?hl=de)
- [Google Workspace-Hilfe: Konto mit Bestätigung in zwei Schritten wiederherstellen](https://support.google.com/a/answer/9176734?hl=de)
- [Google Workspace-Hilfe: Gruppendetails ändern (Adresse, Alias)](https://knowledge.workspace.google.com/admin/groups/update-group-details)
- [Google Workspace-Hilfe: Alias einer Gruppe hinzufügen oder entfernen](https://knowledge.workspace.google.com/admin/groups/give-a-group-an-additional-alias-address)
