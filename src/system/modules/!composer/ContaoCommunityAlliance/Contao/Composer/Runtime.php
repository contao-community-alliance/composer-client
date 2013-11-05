<?php

namespace ContaoCommunityAlliance\Contao\Composer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Version\VersionParser;
use Composer\Util\Filesystem;

/**
 * Class Runtime
 *
 * Composer runtime control.
 */
class Runtime
{
	/**
	 * Initialize the composer environment.
	 */
	static public function initialize()
	{
		if (TL_MODE == 'BE') {
			$GLOBALS['TL_HOOKS']['loadLanguageFile']['composer'] = array(
				'ContaoCommunityAlliance\Contao\Composer\Client',
				'disableOldClientHook'
			);
		}

		if (version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '<')) {
			return;
		}

		// check composer folder exists
		if (!is_dir(COMPOSER_DIR_ABSOULTE)) {
			\Files::getInstance()
				->mkdir(COMPOSER_DIR_RELATIVE);
		}

		// check artifacts folder exists
		if (!is_dir(COMPOSER_DIR_ABSOULTE . '/packages')) {
			\Files::getInstance()
				->mkdir(COMPOSER_DIR_RELATIVE . '/packages');
		}

		// check .htaccess exists
		if (!file_exists(COMPOSER_DIR_ABSOULTE . '/.htaccess')) {
			$strHtaccessContent = <<<EOF
<IfModule !mod_authz_core.c>
  Order deny,allow
  Deny from all
</IfModule>
<IfModule mod_authz_core.c>
  Require all denied
</IfModule>
EOF;

			file_put_contents(COMPOSER_DIR_ABSOULTE . '/.htaccess', $strHtaccessContent);
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

			file_put_contents(COMPOSER_DIR_ABSOULTE . '/composer.json', $strComposerJsonContent);
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

		\ContaoCommunityAlliance\Contao\Composer\Runtime::registerVendorClassLoader();
	}

	/**
	 * Load and install the composer.phar.
	 *
	 * @return bool
	 */
	static public function updateComposer()
	{
		$url  = 'https://getcomposer.org/composer.phar';
		$file = COMPOSER_DIR_ABSOULTE . '/composer.phar';
		Downloader::download($url, $file);
		return true;
	}

	/**
	 * Try to increase memory.
	 *
	 * Inspired by Nils Adermann, Jordi Boggiano
	 *
	 * @see https://github.com/composer/composer/blob/master/bin/composer
	 */
	static public function increaseMemoryLimit()
	{
		if (function_exists('ini_set')) {
			@ini_set('display_errors', 1);

			$memoryInBytes = function ($value) {
				$unit  = strtolower(substr($value, -1, 1));
				$value = (int) $value;
				switch ($unit) {
					case 'g':
						$value *= 1024;
					// no break (cumulative multiplier)
					case 'm':
						$value *= 1024;
					// no break (cumulative multiplier)
					case 'k':
						$value *= 1024;
				}

				return $value;
			};

			$memoryLimit = trim(ini_get('memory_limit'));
			// Increase memory_limit if it is lower than 512M
			if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 512 * 1024 * 1024) {
				@ini_set('memory_limit', '512M');
			}
			unset($memoryInBytes, $memoryLimit);
		}
	}

	/**
	 * Read the stub from the composer.phar and return the warning timestamp.
	 *
	 * @return bool|int
	 */
	static public function readComposerDevWarningTime()
	{
		$configPathname = new \File(COMPOSER_DIR_RELATIVE . '/composer.phar');
		$buffer         = '';
		do {
			$buffer .= fread($configPathname->handle, 1024);
		} while (!preg_match('#define\(\'COMPOSER_DEV_WARNING_TIME\',\s*(\d+)\);#', $buffer, $matches) && !feof(
				$configPathname->handle
			));
		if ($matches[1]) {
			return (int) $matches[1];
		}
		return false;
	}

	/**
	 * Clear the composer cache.
	 *
	 * @param \Input $input
	 */
	static public function clearComposerCache()
	{
		$fs = new Filesystem();
		return $fs->removeDirectory(COMPOSER_DIR_ABSOULTE . '/cache');
	}

	/**
	 * Determinate if safe mode hack is enabled.
	 *
	 * @return bool
	 */
	static public function isSafeModeHackEnabled()
	{
		return (bool) $GLOBALS['TL_CONFIG']['useFTP'];
	}

	/**
	 * Determinate if the php version is supported by composer.
	 *
	 * @return bool
	 */
	static public function isPhpVersionSupported()
	{
		return (bool) version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '>=');
	}

	/**
	 * Determinate if curl is enabled.
	 *
	 * @return bool
	 */
	static public function isCurlEnabled()
	{
		return function_exists('curl_init');
	}

	/**
	 * Determinate if allow_url_fopen is enabled.
	 *
	 * @return bool
	 */
	static public function isAllowUrlFopenEnabled()
	{
		return (bool) ini_get('allow_url_fopen');
	}

	/**
	 * Determinate if apc is enabled.
	 *
	 * @return bool
	 */
	static public function isApcEnabled()
	{
		return function_exists('apc_clear_cache') && !in_array('ini_set', explode(',', ini_get('disable_functions')));
	}

	/**
	 * Determinate if suhosin is enabled.
	 *
	 * @return bool
	 */
	static public function isSuhosinEnabled()
	{
		return extension_loaded('suhosin');
	}

	/**
	 * Check the local environment, return true if everything is fine, an array of errors otherwise.
	 *
	 * @return bool|array
	 */
	static public function checkEnvironment()
	{
		$errors = array();

		if (static::isSafeModeHackEnabled()) {
			$errors[] = $GLOBALS['TL_LANG']['composer_client']['ftp_mode'];
		}

		// check for php version
		if (!static::isPhpVersionSupported()) {
			$errors[] = sprintf($GLOBALS['TL_LANG']['composer_client']['php_version'], PHP_VERSION);
		}

		// check for curl
		if (!static::isCurlEnabled()) {
			$errors[] = $GLOBALS['TL_LANG']['composer_client']['curl_missing'];
		}

		// check for apc and try to disable
		if (static::isApcEnabled() && ini_set('apc.cache_by_default', 0) === false) {
			$errors[] = $GLOBALS['TL_LANG']['composer_client']['could_not_disable_apc'];
		}

		// check for suhosin
		if (static::isSuhosinEnabled()) {
			$errors[] = $GLOBALS['TL_LANG']['composer_client']['suhosin_enabled'];
		}

		if (count($errors)) {
			return $errors;
		}

		return true;
	}

	/**
	 * Register the vendor class loader.
	 */
	static public function registerVendorClassLoader()
	{
		static $registered = false;

		if ($registered) {
			return;
		}

		$registered = true;

		if (file_exists(COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php')) {
			$isContao2 = version_compare(VERSION, '3', '<');

			// unregister contao class loader
			if ($isContao2) {
				spl_autoload_unregister('__autoload');
			}

			// register composer vendor class loader
			require_once(COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php');

			// reregister contao class loader
			if ($isContao2) {
				spl_autoload_register('__autoload');

				// swift is not autoloaded in Contao 2.x
				require_once(TL_ROOT . '/plugins/swiftmailer/classes/Swift.php');
				require_once(TL_ROOT . '/plugins/swiftmailer/swift_init.php');
			}
		}
	}

	/**
	 * Register the composer class loader from composer.phar.
	 */
	static public function registerComposerClassLoader()
	{
		static $registered = false;

		if ($registered) {
			return;
		}

		$registered = true;

		// unregister contao class loader
		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_unregister('__autoload');
		}

		// register composer class loader
		if (file_exists(COMPOSER_DIR_ABSOULTE . '/composer.phar')) {
			$phar             = new \Phar(COMPOSER_DIR_ABSOULTE . '/composer.phar');
			$autoloadPathname = $phar['vendor/autoload.php'];
			require_once($autoloadPathname->getPathname());
		}

		// reregister contao class loader
		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_register('__autoload');
		}
	}

	/**
	 * Load composer and the composer class loader.
	 */
	static function createComposer(IOInterface $io)
	{
		chdir(COMPOSER_DIR_ABSOULTE);

		// try to increase memory limit
		static::increaseMemoryLimit();

		// register composer class loader
		static::registerComposerClassLoader();

		// create composer factory
		/** @var \Composer\Factory $factory */
		$factory = new Factory();

		// create composer
		$composer = $factory->createComposer($io);

		return $composer;
	}

	/**
	 * Update the contao version in the config file and update if necessary.
	 *
	 * @return bool Return true, if the version is updated, false otherwise.
	 */
	static public function updateContaoVersion(Composer $composer, $configPathname)
	{
		/** @var \Composer\Package\RootPackage $package */
		$package       = $composer->getPackage();
		$versionParser = new VersionParser();
		$version       = VERSION . (is_numeric(BUILD) ? '.' . BUILD : '-' . BUILD);
		$prettyVersion = $versionParser->normalize($version);
		if ($package->getVersion() !== $prettyVersion) {
			$configFile            = new JsonFile(TL_ROOT . '/' . $configPathname);
			$configJson            = $configFile->read();
			$configJson['version'] = $version;
			$configFile->write($configJson);

			return true;
		}

		return false;
	}

	/**
	 * Run a process for testing.
	 *
	 * @param string $cmd
	 *
	 * @return bool Return true if the process terminate without error, false otherwise.
	 */
	static public function testProcess($cmd)
	{
		$proc = proc_open(
			$cmd,
			array(
				 array('pipe', 'r'),
				 array('pipe', 'w'),
				 array('pipe', 'w')
			),
			$pipes
		);

		if (is_resource($proc)) {
			return proc_close($proc) != -1;
		}

		return false;
	}
}
