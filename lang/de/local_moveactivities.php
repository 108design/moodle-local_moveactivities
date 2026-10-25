<?php
/**
 * This file is part of a 108design source-available software product.
 *
 * Use and modification are permitted only under the Software License included
 * with this distribution. See LICENSE.md for the full terms.
 *
 * File documentation for lang/de/local_moveactivities.php.
 *
 * @package   local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license   See LICENSE.md for the full terms.
 */
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>. See LICENSE.md.
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Bulk Activity Transfer';
$string['moveactivities'] = 'Bulk Activity Transfer';
$string['moveactivities:move'] = 'Aktivitäten zwischen Kursen kopieren oder verschieben';
$string['sourcecourse'] = 'Quellkurs';
$string['targetcourse'] = 'Zielkurs';
$string['sourcecoursesearch'] = 'Quellkurs suchen';
$string['targetcoursesearch'] = 'Zielkurs suchen';
$string['coursehint'] = 'Gib mindestens zwei Zeichen des Namens, Kurznamens oder der ID-Nummer ein oder eine numerische Kurs-ID. Es erscheinen bis zu 20 passende Kurse.';
$string['activities'] = 'Aktivitäten zum Kopieren oder Verschieben';
$string['activitytypefilter'] = 'Nach Aktivitätstyp filtern';
$string['sectionfilter'] = 'Nach Abschnitt filtern';
$string['selectallvisible'] = 'Alle angezeigten auswählen';
$string['clearselection'] = 'Auswahl aufheben';
$string['resetfilters'] = 'Filter zurücksetzen';
$string['targetsectionmode'] = 'Zielabschnitt';
$string['targetsectionmode_sourcecoursename'] = 'Abschnitt mit dem Namen des Quellkurses verwenden oder anlegen';
$string['targetsectionmode_first'] = 'Ersten Abschnitt verwenden';
$string['execute'] = 'Ausgewählte Aktivitäten kopieren oder verschieben';
$string['enqueue'] = 'Hintergrundauftrag starten';
$string['actionmode'] = 'Aktion';
$string['actionmode_move'] = 'Verschieben (kopieren und Originale löschen)';
$string['actionmode_copy'] = 'Nur kopieren (Originale behalten)';
$string['moveconfirmwarning'] = 'Die ausgewählten Aktivitäten werden im Quellkurs gelöscht.';
$string['moveconfirmquestion'] = 'Beim Verschieben werden die ausgewählten Originale nach der Wiederherstellung im Zielkurs gelöscht. Die wiederhergestellten Aktivitäten erhalten neue IDs. Eine spätere Rückkopie stellt die ursprünglichen Aktivitäts-IDs und die zugehörigen Lerndaten nicht wieder her. Prüfe vor der Bestätigung Quelle, Ziel und Aktivitäten.';
$string['noactivities'] = 'In diesem Quellkurs wurden keine übertragbaren Aktivitäten gefunden.';
$string['resultheader'] = 'Ergebnis der Übertragung';
$string['resultok'] = 'Erfolgreich verschoben';
$string['resultokcopy'] = 'Erfolgreich kopiert';
$string['resulterror'] = 'Fehlgeschlagen';
$string['jobqueued'] = 'Der Auftrag wurde in die Warteschlange gestellt.';
$string['jobstatus'] = 'Auftragsstatus';
$string['jobs'] = 'Hintergrundaufträge';
$string['status'] = 'Status';
$string['created'] = 'Erstellt';
$string['modified'] = 'Zuletzt geändert';
$string['items'] = 'Einträge';
$string['done'] = 'Abgeschlossen';
$string['failed'] = 'Fehlgeschlagen';
$string['queued'] = 'In Warteschlange';
$string['running'] = 'Wird ausgeführt';
$string['done_with_errors'] = 'Mit Fehlern abgeschlossen';
$string['retryfailed'] = 'Fehlgeschlagene Einträge erneut versuchen';
$string['viewjobs'] = 'Aufträge anzeigen';
$string['backtoselection'] = 'Zurück zur Auswahl';
$string['loadactivities'] = 'Aktivitäten laden';
$string['selectsourcefirst'] = 'Wähle einen Quellkurs und klicke auf „Aktivitäten laden“.';
$string['nojobsyet'] = 'Es gibt noch keine Aufträge. Lege einen auf der Auswahlseite an.';
$string['jobid'] = 'Auftrags-ID';
$string['source'] = 'Quelle';
$string['target'] = 'Ziel';
$string['action'] = 'Aktion';
$string['progress'] = 'Fortschritt';
$string['details'] = 'Details';
$string['autorefreshhint'] = 'Während Aufträge laufen, wird diese Seite alle 10 Sekunden aktualisiert.';
$string['refreshnow'] = 'Jetzt aktualisieren';
$string['jobstatus_queued'] = 'In Warteschlange';
$string['jobstatus_running'] = 'Wird ausgeführt';
$string['jobstatus_done'] = 'Abgeschlossen';
$string['jobstatus_done_with_errors'] = 'Mit Fehlern abgeschlossen';
$string['privacy:path'] = 'Aufträge zum Kopieren oder Verschieben';
$string['privacy:metadata:job'] = 'Ein von einer Person angelegter Auftrag zum Kopieren oder Verschieben.';
$string['privacy:metadata:job:userid'] = 'Die ID der Person, die den Auftrag angelegt hat.';
$string['privacy:metadata:job:sourcecourseid'] = 'Die ID des Quellkurses.';
$string['privacy:metadata:job:targetcourseid'] = 'Die ID des Zielkurses.';
$string['privacy:metadata:job:deletesource'] = 'Ob die Originalaktivität nach dem Kopieren gelöscht wird.';
$string['privacy:metadata:job:status'] = 'Der Bearbeitungsstatus des Auftrags.';
$string['privacy:metadata:job:timecreated'] = 'Der Zeitpunkt, zu dem der Auftrag angelegt wurde.';
$string['privacy:metadata:job:timemodified'] = 'Der Zeitpunkt der letzten Änderung des Auftrags.';
$string['privacy:metadata:item'] = 'Eine im Auftrag bearbeitete Aktivität.';
$string['privacy:metadata:item:cmid'] = 'Die Kursmodul-ID der Originalaktivität.';
$string['privacy:metadata:item:activityname'] = 'Der für den Auftrag gespeicherte Aktivitätsname.';
$string['privacy:metadata:item:status'] = 'Der Bearbeitungsstatus der Aktivität.';
$string['privacy:metadata:item:message'] = 'Das Ergebnis oder die Fehlermeldung der Bearbeitung.';
$string['privacy:metadata:item:timecreated'] = 'Der Zeitpunkt, zu dem der Auftragseintrag angelegt wurde.';
$string['privacy:metadata:item:timemodified'] = 'Der Zeitpunkt der letzten Änderung des Auftragseintrags.';
$string['adminhelp'] = 'Hilfe zur Verwaltung';
$string['adminhelpmore'] = 'Mehr dazu im Hilfepanel';
$string['adminhelptopics'] = 'Hilfethemen';
$string['adminhelp_overview'] = 'Aktivitäten übertragen';
$string['adminhelp_overview_article'] = '<p>Wähle Quellkurs, Zielkurs und die gewünschten Aktivitäten. Prüfe vor dem Start, ob die gewählte Aktion Inhalte kopiert oder verschiebt und ob der richtige Zielabschnitt ausgewählt ist.</p><p>Du benötigst die passenden Rechte für Quelle, Ziel und Moodles Sicherungs- beziehungsweise Wiederherstellungsfunktionen. Sichere wichtige Inhalte vor größeren Übertragungen.</p>';
$string['adminhelp_results'] = 'Aufträge und Ergebnisse';
$string['adminhelp_results_article'] = '<p>Prüfe den Auftragsstatus und anschließend die übertragenen Aktivitäten im Zielkurs. Ein gestarteter Auftrag ist noch keine Bestätigung für eine vollständig abgeschlossene Übertragung.</p><p>Bei Fehlern prüfe zunächst das Ergebnis des vorhandenen Auftrags, bevor Du dieselbe Auswahl erneut startest. So vermeidest Du unbeabsichtigte Mehrfachkopien.</p>';
$string['coursesearch'] = 'Nach Name, Kurzname oder Kurs-ID suchen';
$string['confirmmove'] = 'Verschieben bestätigen';
$string['sourcecourse_help'] = 'Suche einen Kurs, in dem Du Aktivitäten übertragen darfst. Gib mindestens zwei Zeichen des Namens, Kurznamens oder der ID-Nummer ein oder die numerische Kurs-ID. Es erscheinen bis zu 20 passende Kurse.';
$string['targetcourse_help'] = 'Wähle einen anderen Zielkurs. Dort benötigst Du die Rechte zum Übertragen und Wiederherstellen von Aktivitäten. Die Kursauswahl ändert Deine Berechtigungen nicht.';
$string['activities_help'] = 'Lade die Aktivitäten des Quellkurses und wähle die gewünschten Einträge aus. Die Auswahlknöpfe betreffen nur die angezeigten Aktivitäten. Lade die Liste nach einem Wechsel des Quellkurses oder der Filter erneut.';
$string['activitytypefilter_help'] = 'Wähle einen oder mehrere Aktivitätstypen und lade die Aktivitäten erneut. Ein leerer Filter berücksichtigt alle Typen.';
$string['sectionfilter_help'] = 'Wähle einen oder mehrere Quellabschnitte und lade die Aktivitäten erneut. Beim Zurücksetzen der Filter bleiben die ausgewählten Kurse erhalten.';
$string['targetsectionmode_help'] = 'Verwende einen vorhandenen Abschnitt mit dem Namen des Quellkurses oder lege bei Bedarf einen an. Alternativ kannst Du den ersten Abschnitt verwenden. Zum Anlegen eines Abschnitts benötigst Du das Recht, den Zielkurs zu bearbeiten.';
$string['actionmode_help'] = 'Kopieren behält die Originale. Verschieben stellt Kopien im Zielkurs wieder her und löscht danach jedes erfolgreich wiederhergestellte Original. Bevor ein Verschiebeauftrag angelegt wird, erscheint eine eigene Bestätigungsseite.';
$string['jobs_help'] = 'Diese Seite zeigt Deine eigenen Aufträge und die Ergebnisse jeder Aktivität. Aufträge in der Warteschlange oder in Bearbeitung benötigen den Moodle-Cron. Prüfe Fehler und Zielkurs vor einem erneuten Versuch, um Mehrfachkopien zu vermeiden.';
$string['adminhelp_overview_help'] = 'Prüfe Quelle, Ziel, Aktivitätsauswahl und die Aktion Kopieren oder Verschieben. Das Hilfepanel erklärt Berechtigungen und Bestätigung.';
$string['adminhelp_results_help'] = 'Ein gestarteter Auftrag ist noch keine abgeschlossene Übertragung. Das Hilfepanel erklärt Fortschritt, Ergebnisse und erneute Versuche.';
