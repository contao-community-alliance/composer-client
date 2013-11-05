<?php

namespace ContaoCommunityAlliance\Contao\Composer;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Console\HtmlOutputFormatter;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\ComposerRepository;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Solver;
use Composer\DependencyResolver\Request;
use Composer\DependencyResolver\SolverProblemsException;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Repository\InstalledArrayRepository;
use Composer\Util\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class Runtime
 *
 * Composer runtime control.
 */
class Runtime
{
	/**
	 * Load and install the composer.phar.
	 *
	 * @return bool
	 */
	static public function updateComposer()
	{
		$url  = 'https://getcomposer.org/composer.phar';
		$file = TL_ROOT . '/composer/composer.phar';
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
		$configPathname = new \File('composer/composer.phar');
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
}
