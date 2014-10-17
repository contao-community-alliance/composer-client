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
 * Class DependencyGraphController
 */
class DependencyGraphController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $repositoryManager = $this->getRepositoryManager();
        /** @var RepositoryInterface $localRepository */
        $localRepository = $repositoryManager->getLocalRepository();

        $dependencyMap = $this->calculateDependencyMap($localRepository);

        $dependencyGraph = array();

        $localPackages = $localRepository->getPackages();

        $localPackages = array_filter(
            $localPackages,
            function (PackageInterface $localPackage) use ($dependencyMap) {
                $name = $localPackage->getName();
                return !isset($dependencyMap[$name])
                       && !($localPackage instanceof \Composer\Package\AliasPackage);
            }
        );

        $allLocalPackages = $localRepository->getPackages();
        $allLocalPackages = array_combine(
            array_map(
                function (PackageInterface $localPackage) {
                    return $localPackage->getName();
                },
                $allLocalPackages
            ),
            $allLocalPackages
        );

        $localPackagesCount = count($localPackages);
        $index              = 0;

        /** @var \Composer\Package\PackageInterface $package */
        foreach ($localPackages as $package) {
            $this->buildDependencyGraph(
                $allLocalPackages,
                $localRepository,
                $package,
                null,
                $package->getPrettyVersion(),
                $dependencyGraph,
                ++$index == $localPackagesCount
            );
        }

        $template                  = new \BackendTemplate('be_composer_client_dependency_graph');
        $template->composer        = $this->composer;
        $template->dependencyGraph = $dependencyGraph;
        return $template->parse();
    }

    /**
     * Build the dependency graph with installed packages.
     *
     * @param RepositoryInterface $repository
     * @param PackageInterface    $package
     * @param array               $dependencyGraph
     */
    protected function buildDependencyGraph(
        array $localPackages,
        RepositoryInterface $repository,
        PackageInterface $package,
        $requiredFrom,
        $requiredConstraint,
        array &$dependencyGraph,
        $isLast,
        $parents = 0,
        $stack = array()
    ) {
        $current = (object) array(
            'package'     => $package,
            'required'    => (object) array(
                'from'       => $requiredFrom,
                'constraint' => $requiredConstraint,
                'parents'    => $parents,
            ),
            'lastInLevel' => $isLast ? $parents - 1 : -1
        );

        if (in_array($package->getName(), $stack)) {
            $current->recursion = true;

            $dependencyGraph[] = $current;
            return;
        }

        $dependencyGraph[] = $current;

        $stack[] = $package->getName();

        $requires      = $package->getRequires();
        $requiresCount = count($requires);
        $index         = 0;
        /** @var string $requireName */
        /** @var \Composer\Package\Link $requireLink */
        foreach ($requires as $requireName => $requireLink) {
            if (isset($localPackages[$requireName])) {
                $this->buildDependencyGraph(
                    $localPackages,
                    $repository,
                    $localPackages[$requireName],
                    $package,
                    $requireLink->getPrettyConstraint(),
                    $dependencyGraph,
                    ++$index == $requiresCount,
                    $parents + 1,
                    $stack
                );
            } else {
                $dependencyGraph[] = (object) array(
                    'package'     => $requireName,
                    'required'    => (object) array(
                        'from'       => $package,
                        'constraint' => $requireLink->getPrettyConstraint(),
                        'parents'    => $parents + 1,
                    ),
                    'lastInLevel' => ++$index == $requiresCount ? $parents : -1
                );
            }
        }
    }
}
