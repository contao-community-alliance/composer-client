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
use Composer\Package\CompletePackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\ComposerRepository;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;
use Composer\DependencyResolver\Pool;
use Symfony\Component\Process\Process;

/**
 * Class ClassLoader
 *
 * Custom class loader that run on Contao 2 and Contao 3.
 */
class ClassLoader
{
	static public function register()
	{
		spl_autoload_register('ContaoCommunityAlliance\Contao\Composer\ClassLoader::load', true, true);
	}

	static public function load($className)
	{
		if (strpos($className, 'ContaoCommunityAlliance\\Contao\\Composer\\') === 0) {
			$className = substr($className, 40);
			$className = str_replace('\\', '/', $className);
			$className .= '.php';

			require(dirname(__FILE__) . '/' . $className);
		}
	}
}
