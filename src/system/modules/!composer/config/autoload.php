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
 * Register classloader
 */
include(TL_ROOT . '/system/modules/!composer/src/ClassLoader.php');
\ContaoCommunityAlliance\Contao\Composer\ClassLoader::register();

/**
 * Register the templates
 */
TemplateLoader::addFiles(
    array
    (
        'be_composer_client_install'          => 'system/modules/!composer/templates',
        'be_composer_client_install_composer' => 'system/modules/!composer/templates',
        'be_composer_client_editor'           => 'system/modules/!composer/templates',
        'be_composer_client_form'             => 'system/modules/!composer/templates',
        'be_composer_client_migrate_undo'     => 'system/modules/!composer/templates',
        'be_composer_client_update'           => 'system/modules/!composer/templates',
        'be_composer_client_settings'         => 'system/modules/!composer/templates',
        'be_composer_client_migrate'          => 'system/modules/!composer/templates',
        'be_composer_client_solve'            => 'system/modules/!composer/templates',
        'be_composer_client_tools'            => 'system/modules/!composer/templates',
        'be_composer_client'                  => 'system/modules/!composer/templates',
        'be_composer_client_errors'           => 'system/modules/!composer/templates',
        'be_composer_client_search'           => 'system/modules/!composer/templates',
        'be_composer_client_dependency_graph' => 'system/modules/!composer/templates',
        'be_composer_client_detached'         => 'system/modules/!composer/templates',
    )
);
