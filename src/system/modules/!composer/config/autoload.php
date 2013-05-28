<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package !composer
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ComposerClient'        => 'system/modules/!composer/ComposerClient.php',
	'ComposerClientBackend' => 'system/modules/!composer/ComposerClientBackend.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_composer_client_search'           => 'system/modules/!composer/templates',
	'be_composer_client_errors'           => 'system/modules/!composer/templates',
	'be_composer_client_solve'            => 'system/modules/!composer/templates',
	'be_composer_client_install'          => 'system/modules/!composer/templates',
	'be_composer_client_install_composer' => 'system/modules/!composer/templates',
	'be_composer_client_update'           => 'system/modules/!composer/templates',
	'be_composer_client'                  => 'system/modules/!composer/templates',
	'be_composer_client_dependency_graph' => 'system/modules/!composer/templates',
	'be_composer_client_form'             => 'system/modules/!composer/templates',
	'be_composer_client_editor'           => 'system/modules/!composer/templates',
));
