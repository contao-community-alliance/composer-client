<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Dominik Zogg <dominik.zogg at gmail.com>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

define('COMPOSER_MIN_PHPVERSION', '5.3.4');
define('COMPOSER_DIR_RELATIVE', 'composer');
define('COMPOSER_DIR_ABSOULTE', TL_ROOT . '/' . COMPOSER_DIR_RELATIVE);

if(version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '>='))
{
    // check composer folder exists
    if(!is_dir(COMPOSER_DIR_ABSOULTE))
    {
        Files::getInstance()->mkdir(COMPOSER_DIR_RELATIVE);
    }

    // check .htaccess exists
    if(!file_exists(COMPOSER_DIR_ABSOULTE . '/.htaccess'))
    {
        $strHtaccessContent = <<<EOF
order deny,allow
deny from all
EOF;

        $strHtaccessFile = new File(COMPOSER_DIR_RELATIVE . '/.htaccess');
        $strHtaccessFile->write($strHtaccessContent);
    }

    // check composer.json exists
    if(!file_exists(COMPOSER_DIR_ABSOULTE . '/composer.json'))
    {
        $strComposerJsonContent = <<<EOF
{
    "require": {
        "contao-community-alliance/composer-installer": "dev-master"
    },
    "minimum-stability": "dev"
}
EOF;

        $objComposerJsonFile = new File(COMPOSER_DIR_RELATIVE . '/composer.json');
        $objComposerJsonFile->write($strComposerJsonContent);
    }

    // check for autoload.php
    if(file_exists(COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php'))
    {
        // register the autoloader
        require COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php';

        // register the default autoloader as spl autoload
        spl_autoload_register('__autoload');
    }
}