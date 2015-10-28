<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
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
