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
