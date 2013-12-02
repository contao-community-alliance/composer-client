<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

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
use ContaoCommunityAlliance\Contao\Composer\Runtime;

/**
 * Class ClearComposerCacheController
 */
class ClearComposerCacheController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		if (Runtime::clearComposerCache()) {
			$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['composerCacheCleared'];
		}

		$this->redirect('contao/main.php?do=composer');
	}
}
