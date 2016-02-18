<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Config;
use Composer\Installer;
use Composer\Package\AliasPackage;
use Composer\Package\PackageInterface;
use ContaoCommunityAlliance\Composer\Plugin\CopyInstaller;
use ContaoCommunityAlliance\Composer\Plugin\Plugin;
use ContaoCommunityAlliance\Composer\Plugin\SymlinkInstaller;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

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
            Messages::addError($GLOBALS['TL_LANG']['composer_client']['pluginNotFound']);
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

                Messages::addInfo(
                    sprintf(
                        $GLOBALS['TL_LANG']['composer_client']['resyncedPackage'],
                        $package->getName()
                    )
                );
            } catch (\RuntimeException $e) {
                Messages::addError(
                    sprintf(
                        $GLOBALS['TL_LANG']['composer_client']['resyncFailed'],
                        $package->getName(),
                        $e->getMessage()
                    )
                );
            }
        }

        $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
        $this->redirect('contao/main.php?do=composer&update=database');
    }
}
