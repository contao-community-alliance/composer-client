<?php

if (!$GLOBALS['TL_CONFIG']['composerAllowRepoClient']) {
	ComposerClient::getInstance()->setInactiveModulesOptionsCallback(
		$GLOBALS['TL_DCA']['tl_settings']['fields']['inactiveModules']['options_callback']
	);
	$GLOBALS['TL_DCA']['tl_settings']['fields']['inactiveModules']['options_callback'] = array('ComposerClient', 'getModules');
}
