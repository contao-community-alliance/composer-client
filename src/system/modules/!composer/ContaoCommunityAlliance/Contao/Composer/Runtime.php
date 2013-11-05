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
}
