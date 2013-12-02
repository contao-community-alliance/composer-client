<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Console\HtmlOutputFormatter;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\Link;
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

/**
 * Class InstalledController
 */
class InstalledController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		$repositoryManager = $this->getRepositoryManager();

		// calculate dependency graph
		$dependencyMap = $this->calculateDependencyMap(
			$repositoryManager->getLocalRepository()
		);

		$replaceMap = $this->calculateReplaceMap(
			$repositoryManager->getLocalRepository()
		);

		$template                = new \BackendTemplate('be_composer_client');
		$template->composer      = $this->composer;
		$template->dependencyMap = $dependencyMap;
		$template->replaceMap    = $replaceMap;
		$template->output        = $_SESSION['COMPOSER_OUTPUT'];

		unset($_SESSION['COMPOSER_OUTPUT']);

		return $template->parse();
	}

	/**
	 * Build replacement map for installed packages.
	 *
	 * @param RepositoryInterface $repository
	 *
	 * @return array
	 */
	protected function calculateReplaceMap(RepositoryInterface $repository, $inverted = false)
	{
		$replaceMap = array();

		/** @var \Composer\Package\PackageInterface $package */
		foreach ($repository->getPackages() as $package) {
			foreach ($package->getReplaces() as $constraint) {
				/** @var Link $constraint */
				$replaceMap[$constraint->getTarget()] = $constraint->getSource();
			}
		}

		return $replaceMap;
	}

}
