<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode'] = array(
	'Ausführungsmodus',
	'Wählen Sie hier, wie composer ausgeführt werden soll.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath'] = array(
	'PHP Pfad/Befehl',
	'Pfad oder Befehl zur PHP Executable.'
);

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline'] = array(
	'im Request Prozess',
	'Composer wird innerhalb des Prozessesaufrufs ausgeführt. Dieses vorgehen ist meist langsamer, funktioniert aber auf allen Systemen, unterliegt allerdings den Laufzeitbeschränkungen des PHP Prozesses.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process'] = array(
	'als Prozessaufruf innerhalb des Requests',
	'Composer wird innerhalb des Prozessaufrufs als externes Programm ausgeführt. Ist meist schneller und auf einigen Systemen möglich, unterliegt allerdings den Laufzeitbeschränkungen des PHP Prozesses.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached'] = array(
	'als eigenständiger Prozess',
	'Composer wird als eigenständiger Prozess gestartet und vom Request abgelöst (detached). Ist nicht auf allen Systemen möglich oder erlaubt (prüfen Sie ob das Starten selbstständiger Prozesse in den AGB Ihres Hosters erlaubt/verboten ist). Diese Methode unterliegt nahezu keinen Begrenzungen, außer der eigenen Geduld ;-)'
);

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_settings']['composer_legend'] = 'Composer-Einstellungen';
