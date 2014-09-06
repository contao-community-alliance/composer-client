<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
use Composer\Config;
use Composer\Console\HtmlOutputFormatter;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Request;
use Composer\DependencyResolver\Solver;
use Composer\DependencyResolver\SolverProblemsException;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\AliasPackage;
use Composer\Package\BasePackage;
use Composer\Package\CompletePackageInterface;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledArrayRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;
use ContaoCommunityAlliance\Composer\Plugin\CopyInstaller;
use ContaoCommunityAlliance\Composer\Plugin\Plugin;
use ContaoCommunityAlliance\Composer\Plugin\SymlinkInstaller;

/**
 * Class ResyncController
 */
class ResyncController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        // get installed packages
        $localRepository = $this->composer->getRepositoryManager()->getLocalRepository();
        $packages        = $localRepository->getPackages();

        // find contao composer plugin
        $plugins = $this->composer->getPluginManager()->getPlugins();
        $plugin  = null;
        foreach ($plugins as $temp) {
            if ($temp instanceof Plugin) {
                $plugin = $temp;
            }
        }

        // plugin not found -> abort
        if (!$plugin) {
            $_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['composer_client']['pluginNotFound'];
            $this->redirect('contao/main.php?do=composer&tools=dialog');
        }

        // create installer
        $config = $this->composer->getConfig();
        if ($config->get('preferred-install') == 'dist') {
            $installer = new CopyInstaller($this->io, $this->composer, $plugin);
        } else {
            $installer = new SymlinkInstaller($this->io, $this->composer, $plugin);
        }

        /** @var PackageInterface[] $packages */
        /** @var Plugin $plugin */

        foreach ($packages as $package) {
            if ($package instanceof AliasPackage) {
                continue;
            }

            try {
                $this->io->write(
                    sprintf(
                        $GLOBALS['TL_LANG']['composer_client']['resyncPackage'],
                        $package->getName()
                    )
                );
                $installer->updateContaoFiles($package);

                $_SESSION['TL_INFO'][] = sprintf(
                    $GLOBALS['TL_LANG']['composer_client']['resyncedPackage'],
                    $package->getName()
                );
            } catch (\RuntimeException $e) {
                $_SESSION['TL_ERROR'][] = sprintf(
                    $GLOBALS['TL_LANG']['composer_client']['resyncFailed'],
                    $package->getName(),
                    $e->getMessage()
                );
            }
        }

        $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
        $this->redirect('contao/main.php?do=composer&update=database');
    }
}
