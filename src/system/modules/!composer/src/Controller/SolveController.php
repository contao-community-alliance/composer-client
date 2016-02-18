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

use Composer\DependencyResolver\DefaultPolicy;
use Composer\DependencyResolver\Request;
use Composer\DependencyResolver\Solver;
use Composer\DependencyResolver\SolverProblemsException;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\AliasPackage;
use Composer\Package\BasePackage;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledArrayRepository;
use Composer\Repository\PlatformRepository;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

/**
 * Class SolveController
 */
class SolveController extends AbstractController
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function handle(\Input $input)
    {
        $packageName = $input->get('solve');
        $version     = base64_decode(rawurldecode($input->get('version')));

        if ($input->post('mark') || $input->post('install')) {
            // make a backup
            copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

            // update requires
            $json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
            $config = $json->read();
            if (!array_key_exists('require', $config)) {
                $config['require'] = array();
            }
            $config['require'][$packageName] = $version;
            $json->write($config);

            Messages::addInfo(
                sprintf($GLOBALS['TL_LANG']['composer_client']['added_candidate'], $packageName, $version)
            );

            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

            if ($input->post('install')) {
                $this->redirect('contao/main.php?do=composer&update=packages');
            }
            $this->redirect('contao/main.php?do=composer');
        }

        /** @var RootPackage $rootPackage */
        $rootPackage = $this->composer->getPackage();

        $installedRootPackage = clone $rootPackage;
        $installedRootPackage->setRequires(array());
        $installedRootPackage->setDevRequires(array());

        $repositoryManager   = $this->getRepositoryManager();
        $localRepository     = $repositoryManager->getLocalRepository();
        $platformRepo        = new PlatformRepository;
        $installedRepository = new CompositeRepository(
            array(
                $localRepository,
                new InstalledArrayRepository(array($installedRootPackage)),
                $platformRepo
            )
        );

        $versionParser = new VersionParser();
        $constraint    = $versionParser->parseConstraints($version);
        $stability     = $versionParser->parseStability($version);

        $aliases = $this->getRootAliases($rootPackage);
        $this->aliasPlatformPackages($platformRepo, $aliases);

        $stabilityFlags               = $rootPackage->getStabilityFlags();
        $stabilityFlags[$packageName] = BasePackage::$stabilities[$stability];

        $pool = $this->getPool($rootPackage->getMinimumStability(), $stabilityFlags);
        $pool->addRepository($installedRepository, $aliases);

        $policy = new DefaultPolicy($rootPackage->getPreferStable());

        $request = new Request($pool);

        // add root package
        $rootPackageConstraint = $this->createConstraint('=', $rootPackage->getVersion());
        $rootPackageConstraint->setPrettyString($rootPackage->getPrettyVersion());
        $request->install($rootPackage->getName(), $rootPackageConstraint);

        // add requirements
        $links = $rootPackage->getRequires();
        /** @var Link $link */
        foreach ($links as $link) {
            if ($link->getTarget() != $packageName) {
                $request->install($link->getTarget(), $link->getConstraint());
            }
        }
        /** @var PackageInterface $package */
        foreach ($installedRepository->getPackages() as $package) {
            $request->install($package->getName(), $this->createConstraint('=', $package->getVersion()));
        }

        $operations = array();
        try {
            $solver = new Solver($policy, $pool, $installedRepository);

            $beforeOperations = $solver->solve($request);

            $request->install($packageName, $constraint);

            $operations = $solver->solve($request);

            /** @var \Composer\DependencyResolver\Operation\SolverOperation $beforeOperation */
            foreach ($beforeOperations as $beforeOperation) {
                /** @var \Composer\DependencyResolver\Operation\InstallOperation $operation */
                foreach ($operations as $index => $operation) {
                    if ($operation
                            ->getPackage()
                            ->getName() != $packageName
                        && $beforeOperation->__toString() == $operation->__toString()
                    ) {
                        unset($operations[$index]);
                    }
                }
            }
        } catch (SolverProblemsException $e) {
            Messages::addError(
                sprintf('<span style="white-space: pre-line">%s</span>', trim($e->getMessage()))
            );
        }

        $template                 = new \BackendTemplate('be_composer_client_solve');
        $template->composer       = $this->composer;
        $template->packageName    = $packageName;
        $template->packageVersion = $version;
        $template->operations     = $operations;
        return $template->parse();
    }

    private function getRootAliases(RootPackageInterface $rootPackage)
    {
        $aliases = $rootPackage->getAliases();

        $normalizedAliases = array();

        foreach ($aliases as $alias) {
            $normalizedAliases[$alias['package']][$alias['version']] = array(
                'alias'            => $alias['alias'],
                'alias_normalized' => $alias['alias_normalized']
            );
        }

        return $normalizedAliases;
    }

    private function aliasPlatformPackages(PlatformRepository $platformRepo, $aliases)
    {
        foreach ($aliases as $package => $versions) {
            foreach ($versions as $version => $alias) {
                $packages = $platformRepo->findPackages($package, $version);
                foreach ($packages as $package) {
                    $aliasPackage = new AliasPackage($package, $alias['alias_normalized'], $alias['alias']);
                    $aliasPackage->setRootPackageAlias(true);
                    $platformRepo->addPackage($aliasPackage);
                }
            }
        }
    }
}
