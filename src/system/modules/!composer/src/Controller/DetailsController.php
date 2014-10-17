<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
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
use Composer\Package\BasePackage;
use Composer\Package\CompletePackageInterface;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledArrayRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;

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

            /*
            $this->redirect(
                'contao/main.php?' . http_build_query(
                    array(
                        'do'      => 'composer',
                        'solve'   => $packageName,
                        'version' => $version
                    )
                )
            );
            */

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

            $_SESSION['TL_INFO'][] = sprintf(
                $GLOBALS['TL_LANG']['composer_client']['added_candidate'],
                $packageName,
                $version
            );

            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

            $this->redirect('contao/main.php?do=composer');
        }

        $installationCandidates = $this->searchPackage($packageName);

        if (empty($installationCandidates)) {
            $_SESSION['TL_ERROR'][] = sprintf(
                $GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates'],
                $packageName
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

                /*
                $versionA = $this->reformatVersion($packageA);
                $versionB = $this->reformatVersion($packageB);

                $classicA = preg_match('#^\d(\.\d+)*$#', $versionA);
                $classicB = preg_match('#^\d(\.\d+)*$#', $versionB);

                $branchA = 'dev-' == substr($packageA->getPrettyVersion(), 0, 4);
                $branchB = 'dev-' == substr($packageB->getPrettyVersion(), 0, 4);

                if ($branchA && $branchB) {
                    return strcasecmp($branchA, $branchB);
                }
                if ($classicA && $classicB) {
                    if ($packageA->getPrettyVersion() == 'dev-master') {
                        return -1;
                    }
                    if ($packageB->getPrettyVersion() == 'dev-master') {
                        return 1;
                    }
                    return version_compare($versionB, $versionA);
                }
                if ($classicA) {
                    return -1;
                }
                if ($classicB) {
                    return 1;
                }
                return 0;
                */
            }
        );

        return $versions;
    }
}
