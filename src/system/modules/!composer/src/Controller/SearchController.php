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
use Composer\Package\CompletePackageInterface;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

/**
 * Class SearchController
 */
class SearchController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $keyword = $input->get('keyword');

        $tokens = explode(' ', $keyword);
        $tokens = array_map('trim', $tokens);
        $tokens = array_filter($tokens);

        $searchName = count($tokens) == 1 && strpos($tokens[0], '/') !== false;

        if (empty($tokens)) {
            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
            $this->redirect('contao/main.php?do=composer');
        }

        $packages = $this->searchPackages(
            $tokens,
            $searchName ? RepositoryInterface::SEARCH_NAME : RepositoryInterface::SEARCH_FULLTEXT
        );

        if (empty($packages)) {
            Messages::addError(
                sprintf($GLOBALS['TL_LANG']['composer_client']['noSearchResult'], $keyword)
            );

            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
            $this->redirect('contao/main.php?do=composer');
        }

        $template           = new \BackendTemplate('be_composer_client_search');
        $template->composer = $this->composer;
        $template->keyword  = $keyword;
        $template->packages = $packages;
        return $template->parse();
    }

    /**
     * Search for packages.
     *
     * @param array $tokens
     * @param int   $searchIn
     *
     * @return CompletePackageInterface[]
     */
    protected function searchPackages(array $tokens, $searchIn)
    {
        $repositoryManager = $this->getRepositoryManager();

        $platformRepo        = new PlatformRepository;
        $localRepository     = $repositoryManager->getLocalRepository();
        $installedRepository = new CompositeRepository(
            array($localRepository, $platformRepo)
        );
        $repositories        = new CompositeRepository(
            array_merge(
                array($installedRepository),
                $repositoryManager->getRepositories()
            )
        );

        $results = $repositories->search(implode(' ', $tokens), $searchIn);

        $contaoVersion = VERSION . (is_numeric(BUILD) ? '.' . BUILD : '-' . BUILD);
        $constraint    = $this->createConstraint('=', $contaoVersion);
        $constraint->setPrettyString($contaoVersion);

        $packages = array();
        foreach ($results as $result) {
            if (!isset($packages[$result['name']])) {
                /** @var PackageInterface[] $versions */
                $versions = $repositories->findPackages($result['name']);

                /** @var PackageInterface|CompletePackageInterface $latestVersion */
                $latestVersion = false;

                $packages[$result['name']] = $result;

                if (count($versions)) {
                    $packages[$result['name']]['type']              = $versions[0]->getType();
                    $packages[$result['name']]['description']       = $versions[0] instanceof CompletePackageInterface
                        ? $versions[0]->getDescription()
                        : '';
                    $packages[$result['name']]['contao-compatible'] = null;

                    foreach ($versions as $version) {
                        $requires = $version->getRequires();

                        if (isset($requires['contao/core']) && $requires['contao/core'] instanceof Link) {
                            /** @var Link $link */
                            $link = $requires['contao/core'];

                            if ($link->getConstraint()->matches($constraint)) {
                                $packages[$result['name']]['contao-compatible'] = true;

                                if (!$latestVersion || $version->getReleaseDate() > $latestVersion->getReleaseDate()) {
                                    $latestVersion = $version;
                                }
                            }
                        }
                    }
                }

                if ($packages[$result['name']]['contao-compatible'] === null) {
                    $packages[$result['name']]['contao-compatible'] = true;
                }

                if ($latestVersion) {
                    $packages[$result['name']]['type'] = $latestVersion->getType();

                    if ($latestVersion instanceof CompletePackageInterface) {
                        $packages[$result['name']]['description'] = $latestVersion->getDescription();
                    }
                }
            }
        }

        return $packages;
    }
}
