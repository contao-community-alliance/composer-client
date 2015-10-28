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
use Composer\Package\AliasPackage;
use Composer\Package\CompletePackage;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Repository\RepositoryInterface;
use ContaoCommunityAlliance\Contao\Composer\ConsoleColorConverter;
use ContaoCommunityAlliance\Contao\Composer\Downloader;

/**
 * Class InstalledController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class InstalledController extends AbstractController
{
    public static $UNMODIFIABLE_PACKAGES = array(
        'contao/core'
    );

    public static $UNDELETABLE_PACKAGES = array(
        'contao/core',
        'contao-community-alliance/composer',
        'contao-community-alliance/composer-client'
    );

    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $repositoryManager = $this->getRepositoryManager();

        // calculate replace map
        $replaceMap = $this->calculateReplaceMap(
            $repositoryManager->getLocalRepository()
        );

        // calculate contao-legacy replace map.
        $legacyReplaceMap = $this->calculateLegacyReplaceMap(
            $repositoryManager->getLocalRepository()
        );

        // build list of explicit required packages
        $requiresList = $this->buildRequiresList(
            $this->composer->getPackage(),
            $replaceMap
        );

        // build list of dependencies
        $dependenciesList = $this->buildDependenciesList(
            $requiresList,
            $repositoryManager->getLocalRepository()
        );

        // build not yet installed package list
        $notInstalledList = $this->buildNotInstalledList(
            $this->composer->getPackage(),
            $repositoryManager->getLocalRepository()
        );

        // calculate dependency graph
        $dependencyMap = $this->calculateDependencyMap(
            $repositoryManager->getLocalRepository()
        );

        // build grouped list of packages
        $groupedPackages = $this->buildGroupedPackagesList(
            $this->composer->getPackage(),
            $repositoryManager->getLocalRepository(),
            $requiresList,
            $dependencyMap,
            $notInstalledList
        );

        $template                   = new \BackendTemplate('be_composer_client');
        $template->composer         = $this->composer;
        $template->dependencyMap    = $dependencyMap;
        $template->replaceMap       = $replaceMap;
        $template->legacyReplaceMap = $legacyReplaceMap;
        $template->groupedPackages  = $groupedPackages;
        $template->requiresList     = $requiresList;
        $template->dependenciesList = $dependenciesList;
        if ('detached' !== $GLOBALS['TL_CONFIG']['composerExecutionMode']) {
            $template->output = $_SESSION['COMPOSER_OUTPUT'];
        }

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
    protected function calculateReplaceMap(RepositoryInterface $repository)
    {
        $replaceMap = array();

        /** @var \Composer\Package\PackageInterface $package */
        foreach ($repository->getPackages() as $package) {
            foreach ($package->getReplaces() as $constraint) {
                /** @var Link $constraint */
                if (isset($replaceMap[$constraint->getTarget()])) {
                    $replaceMap[$constraint->getTarget()][] = $constraint->getSource();
                } else {
                    $replaceMap[$constraint->getTarget()][] = array($constraint->getSource());
                }
            }
        }

        return $replaceMap;
    }

    /**
     * Build replacement map for installed packages.
     *
     * @param RepositoryInterface $repository
     *
     * @return array
     */
    protected function calculateLegacyReplaceMap(RepositoryInterface $repository)
    {
        $replaceMap = array();
        $legacyReplaces = json_decode(
            Downloader::download('http://legacy-packages-via.contao-community-alliance.org/abandoned.json'),
            true
        );

        /** @var \Composer\Package\PackageInterface $package */
        foreach ($repository->getPackages() as $package) {
            if (isset($legacyReplaces[$package->getName()])) {
                $replaceMap[$package->getName()] = $legacyReplaces[$package->getName()];
            }
        }

        return $replaceMap;
    }

    /**
     * Group packages in a repository by vendor and return a sorted and grouped list.
     *
     * @param RepositoryInterface $repository
     *
     * @return array
     */
    protected function buildGroupedPackagesList(
        RootPackageInterface $rootPackage,
        RepositoryInterface $repository,
        $requiresList,
        $dependencyMap,
        $notInstalledList
    ) {
        $groupedPackages = array();

        $requires     = $rootPackage->getRequires();
        $extra        = $rootPackage->getExtra();
        $versionLocks = isset($extra['contao']['version-locks']) ? (array) $extra['contao']['version-locks'] : array();

        /** @var \Composer\Package\PackageInterface $package */
        foreach ($repository->getPackages() as $package) {
            // skip aliases
            if ($package instanceof AliasPackage) {
                continue;
            }

            $name = $package->getPrettyName();
            list($group) = explode('/', $name);

            $dependencyOf = false;
            if (isset($dependencyMap[$package->getName()])) {
                $dependencyOf = $dependencyMap[$package->getName()];
            }
            if (count($package->getReplaces())) {
                foreach (array_keys($package->getReplaces()) as $replace) {
                    if (isset($dependencyMap[$replace])) {
                        $dependencyOf = $dependencyMap[$replace];
                        break;
                    }
                }
            }

            $item = (object) array(
                'group'             => $group,
                'name'              => $package->getPrettyName(),
                'package'           => $package,
                'dependencyOf'      => $dependencyOf,
                'installing'        => false,
                'removeable'        => in_array($name, $requiresList),
                'removing'          => !in_array($name, $requiresList) && !isset($dependencyMap[$name]),
                'pinable'           => $package->getStability() != 'dev',
                'pinned'            => array_key_exists($name, $versionLocks),
                'versionConstraint' => $this->createConstraint('=', $package->getVersion()),
                'requireConstraint' => isset($requires[$package->getName()]) ? $requires[$package->getName(
                )]->getConstraint() : false,
            );

            if (isset($groupedPackages[$group])) {
                $groupedPackages[$group][] = $item;
            } else {
                $groupedPackages[$group] = array($item);
            }
        }

        /** @var Link $notInstalledPackageConstraint */
        foreach ($notInstalledList as $notInstalledPackageName => $notInstalledPackageConstraint) {
            list($group) = explode('/', $notInstalledPackageName);

            $package = new CompletePackage(
                $notInstalledPackageName,
                $notInstalledPackageConstraint->getPrettyConstraint(),
                $notInstalledPackageConstraint->getPrettyConstraint()
            );

            $item = (object) array(
                'group'             => $group,
                'name'              => $notInstalledPackageName,
                'version'           => $notInstalledPackageConstraint->getPrettyConstraint(),
                'package'           => $package,
                'dependencyOf'      => false,
                'installing'        => true,
                'removeable'        => true,
                'removing'          => false,
                'pinable'           => false,
                'pinned'            => false,
                'versionConstraint' => $this->createConstraint('=', $package->getVersion()),
                'requireConstraint' => false,
            );

            if (isset($groupedPackages[$group])) {
                $groupedPackages[$group][] = $item;
            } else {
                $groupedPackages[$group] = array($item);
            }
        }

        foreach (array_keys($groupedPackages) as $group) {
            usort(
                $groupedPackages[$group],
                function ($a, $b) {
                    return strnatcasecmp($a->package->getPrettyName(), $b->package->getPrettyName());
                }
            );
        }

        uksort($groupedPackages, 'strnatcasecmp');

        return $groupedPackages;
    }

    /**
     * Build a list of all explicit required packages.
     *
     * @param RepositoryInterface $repository
     *
     * @return array
     */
    public function buildRequiresList(RootPackageInterface $rootPackage, $replaceMap)
    {
        $requires = array_keys($rootPackage->getRequires());

        foreach ($requires as $packageName) {
            if (isset($replaceMap[$packageName])) {
                foreach ($replaceMap[$packageName] as $replaceName) {
                    $requires[] = $replaceName;
                }
            }
        }

        return array_combine($requires, $requires);
    }

    /**
     * Build a list of all depended packages.
     *
     * @param RepositoryInterface $repository
     *
     * @return array
     */
    public function buildDependenciesList($requiresList, RepositoryInterface $repository)
    {
        $dependencies = array();

        /** @var \Composer\Package\PackageInterface $package */
        foreach ($repository->getPackages() as $package) {
            // skip aliases
            if ($package instanceof AliasPackage) {
                continue;
            }

            $name = $package->getName();

            // skip explicit required packages
            if (isset($requiresList[$name])) {
                continue;
            }

            $dependencies[$name] = $name;
        }

        return $dependencies;
    }

    /**
     * Build a list of not yet installed packages.
     *
     * @param RepositoryInterface $repository
     *
     * @return array
     */
    public function buildNotInstalledList(RootPackageInterface $rootPackage, RepositoryInterface $localRepository)
    {
        $requires = $rootPackage->getRequires();

        $notInstalledList = array();

        /** @var Link $requiredConstraint */
        foreach ($requires as $requiredName => $requiredConstraint) {
            $packages = $localRepository->findPackages($requiredName);

            if (empty($packages)) {
                $notInstalledList[$requiredName] = $requiredConstraint;
            }
        }

        return $notInstalledList;
    }

    /**
     * Compare two packages by their names.
     *
     * @param PackageInterface $left
     * @param PackageInterface $right
     *
     * @return int
     */
    public function packageCompare(PackageInterface $left, PackageInterface $right)
    {
        return strnatcasecmp($left->getPrettyName(), $right->getPrettyName());
    }
}
