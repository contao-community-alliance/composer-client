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
use Composer\Json\JsonFile;
use Composer\Package\AliasPackage;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;

/**
 * Class PinController
 */
class PinController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $packageName = $input->post('pin');

        $json         = new JsonFile(TL_ROOT . '/' . $this->configPathname);
        $config       = $json->read();
        $versionLocks = isset($config['extra']['contao']['version-locks'])
            ? (array) $config['extra']['contao']['version-locks'] : array();

        if (array_key_exists($packageName, $versionLocks)) {
            if ($versionLocks[$packageName] === null) {
                unset($config['require'][$packageName]);
            } else {
                $config['require'][$packageName] = $versionLocks[$packageName];
            }
            unset($versionLocks[$packageName]);
        } else {
            /** @var RepositoryInterface $localRepository */
            $localRepository = $this->getRepositoryManager()->getLocalRepository();
            /** @var PackageInterface[] $packages */
            $packages = $localRepository->findPackages($packageName);

            while (count($packages) && $packages[0] instanceof AliasPackage) {
                array_shift($packages);
            }
            if (empty($packages)) {
                $this->redirect('contao/main.php?do=composer');
            }

            if (isset($config['require'][$packageName])) {
                $versionLocks[$packageName] = $config['require'][$packageName];
            } else {
                $versionLocks[$packageName] = null;
            }

            $config['require'][$packageName] = $packages[0]->getVersion();
        }

        if (empty($versionLocks)) {
            unset($config['extra']['contao']['version-locks']);
        } else {
            $config['extra']['contao']['version-locks'] = $versionLocks;
        }

        // make a backup
        copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

        // update config file
        $json->write($config);

        $this->redirect('contao/main.php?do=composer');
    }
}
