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

use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\PackageInterface;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

/**
 * Class DetailsController
 */
class DetailsController extends AbstractController
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function handle(\Input $input)
    {
        $packageName = $input->get('install');

        if ($packageName == 'contao/core') {
            $this->redirect('contao/main.php?do=composer');
        }

        if ($input->post('version')) {
            $version = base64_decode(rawurldecode($input->post('version')));

            // make a backup
            copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

            // update requires
            $json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
            $config = $json->read();
            if (!array_key_exists('require', $config)) {
                $config['require'] = array();
            }
            $config['require'][$packageName] = $version;
            ksort($config['require']);
            $json->write($config);

            Messages::addInfo(
                sprintf($GLOBALS['TL_LANG']['composer_client']['added_candidate'], $packageName, $version)
            );

            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

            $this->redirect('contao/main.php?do=composer');
        }

        $installationCandidates = $this->searchPackage($packageName);

        if (empty($installationCandidates)) {
            Messages::addError(
                sprintf($GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates'], $packageName)
            );

            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
            $this->redirect('contao/main.php?do=composer');
        }

        $template              = new \BackendTemplate('be_composer_client_install');
        $template->composer    = $this->composer;
        $template->packageName = $packageName;
        $template->candidates  = $installationCandidates;
        return $template->parse();
    }

    /**
     * Search for a single packages versions.
     *
     * @param string $packageName
     *
     * @return PackageInterface[]
     */
    protected function searchPackage($packageName)
    {
        $pool = $this->getPool();

        $versions = array();
        $seen     = array();
        $matches  = $pool->whatProvides($packageName);
        foreach ($matches as $package) {
            /** @var PackageInterface $package */
            // skip providers/replacers
            if ($package->getName() !== $packageName) {
                continue;
            }
            // add each version only once to skip installed version.
            if (!in_array($package->getPrettyVersion(), $seen)) {
                $seen[]     = $package->getPrettyVersion();
                $versions[] = $package;
            }
        }

        usort(
            $versions,
            function (PackageInterface $packageA, PackageInterface $packageB) {
                // is this a wise idea?
                if (($dsa = $packageA->getReleaseDate()) && ($dsb = $packageB->getReleaseDate())) {
                    /** @var \DateTime $dsa */
                    /** @var \DateTime $dsb */
                    return $dsb->getTimestamp() - $dsa->getTimestamp();
                }
            }
        );

        return $versions;
    }
}
