<?php
// This file is part of Moodle - https://moodle.org/
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

$string['coursehint'] = 'Gib mindestens zwei Zeichen des Namens, Kurznamens oder der ID-Nummer ein oder eine numerische Kurs-ID. Es erscheinen bis zu 20 passende Kurse.';
$string['moveconfirmquestion'] = 'Beim Verschieben werden die ausgewählten Originale nach der Wiederherstellung im Zielkurs gelöscht. Die wiederhergestellten Aktivitäten erhalten neue IDs. Eine spätere Rückkopie stellt die ursprünglichen Aktivitäts-IDs und die zugehörigen Lerndaten nicht wieder her. Prüfe vor der Bestätigung Quelle, Ziel und Aktivitäten.';
$string['selectsourcefirst'] = 'Wähle einen Quellkurs und klicke auf „Aktivitäten laden“.';
$string['nojobsyet'] = 'Es gibt noch keine Aufträge. Lege einen auf der Auswahlseite an.';
$string['adminhelp_overview_article'] = '<p>Wähle Quellkurs, Zielkurs und die gewünschten Aktivitäten. Prüfe vor dem Start, ob die gewählte Aktion Inhalte kopiert oder verschiebt und ob der richtige Zielabschnitt ausgewählt ist.</p><p>Du benötigst die passenden Rechte für Quelle, Ziel und Moodles Sicherungs- beziehungsweise Wiederherstellungsfunktionen. Sichere wichtige Inhalte vor größeren Übertragungen.</p>';
$string['adminhelp_results_article'] = '<p>Prüfe den Auftragsstatus und anschließend die übertragenen Aktivitäten im Zielkurs. Ein gestarteter Auftrag ist noch keine Bestätigung für eine vollständig abgeschlossene Übertragung.</p><p>Bei Fehlern prüfst du zunächst das Ergebnis des vorhandenen Auftrags, bevor du dieselbe Auswahl erneut startest. So vermeidest du unbeabsichtigte Mehrfachkopien.</p>';
$string['sourcecourse_help'] = 'Suche einen Kurs, in dem du Aktivitäten übertragen darfst. Gib mindestens zwei Zeichen des Namens, Kurznamens oder der ID-Nummer ein oder die numerische Kurs-ID. Es erscheinen bis zu 20 passende Kurse.';
$string['targetcourse_help'] = 'Wähle einen anderen Zielkurs. Dort benötigst du die Rechte zum Übertragen und Wiederherstellen von Aktivitäten. Die Kursauswahl ändert deine Berechtigungen nicht.';
$string['activities_help'] = 'Lade die Aktivitäten des Quellkurses und wähle die gewünschten Einträge aus. Die Auswahlknöpfe betreffen nur die angezeigten Aktivitäten. Lade die Liste nach einem Wechsel des Quellkurses oder der Filter erneut.';
$string['activitytypefilter_help'] = 'Wähle einen oder mehrere Aktivitätstypen und lade die Aktivitäten erneut. Ein leerer Filter berücksichtigt alle Typen.';
$string['sectionfilter_help'] = 'Wähle einen oder mehrere Quellabschnitte und lade die Aktivitäten erneut. Beim Zurücksetzen der Filter bleiben die ausgewählten Kurse erhalten.';
$string['targetsectionmode_help'] = 'Verwende einen vorhandenen Abschnitt mit dem Namen des Quellkurses oder lege bei Bedarf einen an. Alternativ kannst du den ersten Abschnitt verwenden. Zum Anlegen eines Abschnitts benötigst du das Recht, den Zielkurs zu bearbeiten.';
$string['jobs_help'] = 'Diese Seite zeigt deine eigenen Aufträge und die Ergebnisse jeder Aktivität. Aufträge in der Warteschlange oder in Bearbeitung benötigen den Moodle-Cron. Prüfe Fehler und Zielkurs vor einem erneuten Versuch, um Mehrfachkopien zu vermeiden.';
$string['adminhelp_overview_help'] = 'Prüfe Quelle, Ziel, Aktivitätsauswahl und die Aktion Kopieren oder Verschieben. Das Hilfepanel erklärt Berechtigungen und Bestätigung.';
$string['adminhelp_results_help'] = 'Ein gestarteter Auftrag ist noch keine abgeschlossene Übertragung. Das Hilfepanel erklärt Fortschritt, Ergebnisse und erneute Versuche.';
$string['updateguard_hint'] = 'Verwende ein freigegebenes Paket für die installierte Ausgabe. Du kannst den Vorgang erneut prüfen.';
$string['updateguard_dependency'] = 'Eine benötigte Plugin-Version fehlt. Aktualisiere die Abhängigkeit gemeinsam mit diesem Plugin oder vorher.';
$string['updateguard_approval'] = 'Die Pakete oder der installierte Stand haben sich seit der Vorschau geändert. Prüfe den Vorgang erneut.';
$string['updateguard_busy'] = 'Eine andere Installation läuft. Versuche es nach deren Abschluss erneut.';
$string['updateguard_installed'] = 'Der installierte Updateschutz ist unvollständig. Stelle die Dateien der zuletzt installierten freigegebenen Version wieder her.';
$string['updateguard_operation'] = 'Diese Kombination von Installationsaktionen wird nicht unterstützt. Starte einen einzelnen Updatevorgang erneut.';
$string['updateinfo_choice'] = 'Kontrolliere vor dem Fortfahren die Paketquelle und Moodles Prüfergebnisse. Mit „{$a}“ unten kannst du abbrechen.';
