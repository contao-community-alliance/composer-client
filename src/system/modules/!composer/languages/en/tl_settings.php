<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']      = array(
    'Update composer library automatically',
    'The composer library (also known as <code>composer.phar</code>) will be updated after 30 days automatically.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']          = array(
    'Execution mode',
    'Please select how the composer binary shall get executed.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']                = array(
    'PHP Path/Command',
    'Path or command to the php binary.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables'] = array(
    'Remove repository client tables',
    'The old ER2 repository client tables will not be removed by the composer client database update tool, ' .
    'until you enable this checkbox.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerProfiling']              = array(
    'Enable profiling',
    'Display timing and memory usage information.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables'] = array(
    'Remove repository client tables',
    'The old ER2 repository client tables will not be removed by the composer client database update tool, ' .
    'until you enable this checkbox.'
);


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']   = array(
    'In the HTTP request.',
    'Composer will get executed within the web server process. ' .
    'This mode is usually slower but works for all systems but is subject to the maximum runtime limitations of PHP.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']  = array(
    'as sub process of the web server process',
    'Composer will get executed via sub process call as external program. ' .
    'This is usually faster but only possible on systems supporting proc_open() and ' .
    'is subject to the maximum runtime limitations of PHP.'
);
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached'] = array(
    'as standalone process',
    'Composer will get executed as standalone sub process and detached into the background. ' .
    'This is not possible or allowed on some systems (please check with your provider if ' .
    'it is allowed to spawn background processes). This method has nearly no limitations.'
);

$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET'] = array(
    'Be quiet!',
    'Do not output any messages.'
);

$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL'] = array(
    'Default verbosity',
    'The default verbosity level - use this when you are not experiencing any problems.'
);

$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE'] = array(
    'Be verbose',
    'Increased verbosity of messages.'
);

$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE'] = array(
    'Be very verbose',
    'Informative non essential messages.'
);

$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG'] = array(
    'Debug messages',
    'This will show all messages, including debug messages that most likely will be of no use to the average user.'
);

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_settings']['composer_legend'] = 'Composer settings';
