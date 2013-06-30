<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Dominik Zogg <dominik.zogg at gmail.com>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

define('COMPOSER_MIN_PHPVERSION', '5.3.4');
define('COMPOSER_DIR_RELATIVE', 'composer');
define('COMPOSER_DIR_ABSOULTE', TL_ROOT . '/' . COMPOSER_DIR_RELATIVE);

/**
 * Create initial composer.json
 */
if (version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '>=')) {
	if (TL_MODE == 'BE') {
		$GLOBALS['TL_HOOKS']['loadLanguageFile']['composer'] = array('ComposerClient', 'disableOldClientHook');
	}

	// check composer folder exists
	if (!is_dir(COMPOSER_DIR_ABSOULTE)) {
		Files::getInstance()
			->mkdir(COMPOSER_DIR_RELATIVE);
	}
	if (!is_dir(COMPOSER_DIR_ABSOULTE . '/packages')) {
		Files::getInstance()
			->mkdir(COMPOSER_DIR_RELATIVE . '/packages');
	}

	// check .htaccess exists
	if (!file_exists(COMPOSER_DIR_ABSOULTE . '/.htaccess')) {
		$strHtaccessContent = <<<EOF
order deny,allow
deny from all
EOF;

		$strHtaccessFile = new File(COMPOSER_DIR_RELATIVE . '/.htaccess');
		$strHtaccessFile->write($strHtaccessContent);
	}

	// check composer.json exists
	if (!file_exists(COMPOSER_DIR_ABSOULTE . '/composer.json')) {
		$strContaoVersion = VERSION . (is_numeric(BUILD) ? '.' . BUILD : '-' . BUILD);

		$strComposerJsonContent = <<<EOF
{
    "name": "contao/core",
    "description": "Contao Open Source CMS",
    "license": "LGPL-3.0+",
    "version": "$strContaoVersion",
    "type": "metapackage",
    "require": {
        "contao-community-alliance/composer": "dev-master@dev"
    },
    "scripts": {
        "pre-update-cmd": "ContaoCommunityAlliance\\\\ComposerInstaller\\\\ModuleInstaller::preUpdate",
        "post-update-cmd": "ContaoCommunityAlliance\\\\ComposerInstaller\\\\ModuleInstaller::postUpdate",
        "post-autoload-dump": "ContaoCommunityAlliance\\\\ComposerInstaller\\\\ModuleInstaller::postAutoloadDump"
    },
    "config": {
        "preferred-install": "dist",
    	"cache-dir": "cache"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "http://legacy-packages-via.contao-community-alliance.org/"
        },
        {
            "type": "artifact",
            "url": "packages/"
        }
    ]
}
EOF;

		$objComposerJsonFile = new File(COMPOSER_DIR_RELATIVE . '/composer.json');
		$objComposerJsonFile->write($strComposerJsonContent);
	}

	// check for autoload.php
	if (file_exists(COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php')) {
		// unregister the default autoloader
		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_unregister('__autoload');
		}

		// register the autoloader
		require COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php';

		// register the default autoloader as spl autoload
		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_register('__autoload');
		}
	}

	if (!getenv('COMPOSER_HOME')) {
		putenv('COMPOSER_HOME=' . COMPOSER_DIR_ABSOULTE);
	}

	// see #54
	if (!getenv('PATH')) {
		if (defined('PHP_WINDOWS_VERSION_BUILD')) {
			putenv('PATH=%SystemRoot%\system32;%SystemRoot%;%SystemRoot%\System32\Wbem');
		}
		else {
			putenv('PATH=/opt/local/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin');
		}
	}
}

/**
 * Add backend module
 */
$GLOBALS['BE_MOD']['system']['composer'] = array(
	'callback'   => 'ComposerClientBackend',
	'icon'       => 'system/modules/!composer/assets/images/icon.png',
	'stylesheet' => 'system/modules/!composer/assets/css/backend.css',
);
