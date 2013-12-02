<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
use Composer\Downloader\DownloadManager;
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
use ContaoCommunityAlliance\Composer\Plugin\ConfigUpdateException;

/**
 * Class UpdatePackagesController
 */
class UpdatePackagesController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		try {
			if (version_compare(VERSION, '3', '<')) {
				spl_autoload_unregister('__autoload');
			}

			$lockPathname = preg_replace('#\.json$#', '.lock', $this->configPathname);

			/** @var DownloadManager $downloadManager */
			$downloadManager = $this->composer->getDownloadManager();
			$downloadManager->setOutputProgress(false);

			$installer = Installer::create($this->io, $this->composer);

			switch ($this->composer->getConfig()
				->get('preferred-install')) {
				case 'source':
					$installer->setPreferSource(true);
					break;
				case 'dist':
					$installer->setPreferDist(true);
					break;
				case 'auto':
				default:
					// noop
					break;
			}

			if (file_exists(TL_ROOT . '/' . $lockPathname)) {
				$installer->setUpdate(true);
			}

			if ($installer->run()) {
				$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

				// redirect to database update
				$this->redirect('contao/main.php?do=composer&update=database');
			}
			else {
				$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

				$this->redirect('contao/main.php?do=composer');
			}
		}
		catch (ConfigUpdateException $e) {
			do {
				$_SESSION['TL_CONFIRM'][] = str_replace(TL_ROOT, '', $e->getMessage());
				$e                        = $e->getPrevious();
			}
			while ($e);
			$_SESSION['TL_INFO'][] = $GLOBALS['TL_LANG']['composer_client']['restartOperation'];
			$this->redirect('contao/main.php?do=composer');
		}
		catch (\RuntimeException $e) {
			do {
				$_SESSION['TL_ERROR'][] = str_replace(TL_ROOT, '', $e->getMessage());
				$e                      = $e->getPrevious();
			}
			while ($e);
			$this->redirect('contao/main.php?do=composer');
		}
	}
}
