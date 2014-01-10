<?php

if (!$GLOBALS['TL_CONFIG']['composerAllowRepoClient']) {
	ContaoCommunityAlliance\Contao\Composer\Client::getInstance()
		->setInactiveModulesOptionsCallback(
			$GLOBALS['TL_DCA']['tl_settings']['fields']['inactiveModules']['options_callback']
		);
	$GLOBALS['TL_DCA']['tl_settings']['fields']['inactiveModules']['options_callback'] = array(
		'ContaoCommunityAlliance\Contao\Composer\Client',
		'getModules'
	);
}


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'composerExecutionMode';

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{composer_legend:hide},composerExecutionMode';

$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['composerExecutionMode_process']  = 'composerPhpPath';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['composerExecutionMode_detached'] = 'composerPhpPath';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['composerExecutionMode'] = array(
	'label'     => $GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode'],
	'inputType' => 'select',
	'options'   => array('inline', 'process', 'detached'),
	'reference' => $GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes'],
	'eval'      => array(
		'mandatory'      => true,
		'tl_class'       => 'w50',
		'helpwizard'     => true,
		'submitOnChange' => true,
	),
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['composerPhpPath']       = array(
	'label'     => $GLOBALS['TL_LANG']['tl_settings']['composerPhpPath'],
	'inputType' => 'text',
	'eval'      => array(
		'mandatory'      => true,
		'tl_class'       => 'w50',
        'allowHtml'      => true,
        'preserveTags'   => true,
        'decodeEntities' => true,
	),
);
