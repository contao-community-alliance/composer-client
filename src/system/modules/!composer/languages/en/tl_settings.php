<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode'] = array(
	'Execution mode',
	'Please select how the composer binary shall get executed.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath'] = array(
	'PHP Path/Command',
	'Path or command to the php binary.'
);

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline'] = array(
	'In the HTTP request.',
	'Composer will get executed within the web server process. This mode is usually slower but works for all systems but is subject to the maximum runtime limitations of PHP.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process'] = array(
	'as sub process of the web server process',
	'Composer will get executed via sub process call as external program. This is usually faster and possible on some systems but is subject to the maximum runtime limitations of PHP.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached'] = array(
	'as standalone process',
	'Composer will get executed as standalone sub process and detached into the background. This is not possible or allowed on some systems (please check with your provider if it is allowed to spawn background processes). This method has nearly no limitations except for the personal patience. ;)'
);

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_settings']['composer_legend'] = 'Composer settings';
