<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/composer/language/de/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-05-23T03:00:44+02:00
 */

$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']['0']                         = 'Composer Bibliothek automatisch aktualisieren';
$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']['1']                         = 'Die Composer Bibliothek (auch bekannt als <code>composer.phar</code>) wird automatisch nach 30 Tagen aktualisiert.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['0']                             = 'Ausführungsmodus';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['1']                             = 'Wählen Sie den Ausführungsmodus der Composer Binärdatei aus.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['0']                = 'als eigenständiger Prozess';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['1']                = 'Composer wird als eigenständiger Unterprozess unabhängig im Hintergrund ausgeführt. Dies ist leider auf einigen Systemen nicht erlaubt bzw. nicht möglich. (Bitte fragen Sie Ihren Provider, ob Ihr Webspace das Ausführen von Hintergrundprozessen erlaubt.) Diese Methode hat nahezu keine Einschränkungen.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['0']                  = 'während der aktuellen http-Anfrage';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['1']                  = 'Composer wird innerhalb des Webserverprozesses ausgeführt. Dieser Modus ist in der Regel langsamer, aber funktioniert für alle Systeme. Er wird lediglich von den PHP-Laufzeitlimitierungen begrenzt.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['0']                 = 'als Unterprozess des aktuellen Webserverprozesses';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['1']                 = 'Composer wird als Unterprozessaufruf als externes Programm ausgeführt. Dieser Modus ist in der Regel schneller, aber nur auf Systemen lauffähig, die proc_open() unterstützen. Er wird zusätzlich von den PHP-Laufzeitlimitierungen begrenzt.';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['0']                                   = 'PHP-Pfad/Befehl';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['1']                                   = 'Der Pfad oder Befehl zum PHP-Binary.';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['0']                    = 'Extension Repository Tabellen löschen';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['1']                    = 'Bei Aktivierung werden die alten ER2-Tabellen im Composer Datenbank-Update-Tool bei der nächsten Aktualisierung zum Löschen angeboten.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosity']['0']                                 = 'Ausführlichkeit der Protokollierung';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosity']['1']                                 = 'Die Konsole hat 5 Stufen der Ausführlichkeit. Wenn Sie keine Probleme bei der Benutzung der Paketverwaltung haben, sollten Sie diese Einstellung bei der Standard-Stufe belassen.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['0']        = 'Debug-Ausgabe';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['1']        = 'Diese Stufe wird alle Meldungen anzeigen. Diese Meldungen sind für den Standard-Benutzer uninteressant.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['0']       = 'Standard Ausführlichkeit';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['1']       = 'Die Standard-Stufe der Ausführlichkeit - diese sollten Sie benutzen, wenn Sie keine Probleme mit der Paketverwaltung haben.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['0']        = 'Halt die Klappe! :-)';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['1']        = 'Es erfolgt keine Ausgabe.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['0']      = 'Umfangreiche Ausgabe';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['1']      = 'Es erfolgt eine umfangreiche Ausgabe von Meldungen.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['0'] = 'Sehr umfangreiche Ausgabe';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['1'] = 'Es erfolgt eine sehr umfangreiche Ausgabe von rein informativen Meldungen.';
$GLOBALS['TL_LANG']['tl_settings']['composer_legend']                                        = 'Composer-Einstellungen';

