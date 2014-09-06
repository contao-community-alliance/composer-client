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
 * Class ExpertsEditorController
 */
class ExpertsEditorController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $configFile = new \File($this->configPathname);

        if ($input->post('save')) {
            $tempPathname = $this->configPathname . '~';
            $tempFile     = new \File($tempPathname);

            $config = $input->postRaw('config');
            $config = html_entity_decode($config, ENT_QUOTES, 'UTF-8');

            $tempFile->write($config);
            $tempFile->close();

            $validator = new ConfigValidator($this->io);
            list($errors, $publishErrors, $warnings) = $validator->validate(TL_ROOT . '/' . $tempPathname);

            if (!$errors && !$publishErrors) {
                $_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['configValid'];
                $this->import('Files');
                $this->Files->rename($tempPathname, $this->configPathname);
            } else {
                $tempFile->delete();
                $_SESSION['COMPOSER_EDIT_CONFIG'] = $config;

                if ($errors) {
                    foreach ($errors as $message) {
                        $_SESSION['TL_ERROR'][] = 'Error: ' . $message;
                    }
                }

                if ($publishErrors) {
                    foreach ($publishErrors as $message) {
                        $_SESSION['TL_ERROR'][] = 'Publish error: ' . $message;
                    }
                }
            }

            if ($warnings) {
                foreach ($warnings as $message) {
                    $_SESSION['TL_ERROR'][] = 'Warning: ' . $message;
                }
            }

            $this->reload();
        }

        if (isset($_SESSION['COMPOSER_EDIT_CONFIG'])) {
            $config = $_SESSION['COMPOSER_EDIT_CONFIG'];
            unset($_SESSION['COMPOSER_EDIT_CONFIG']);
        } else {
            $config = $configFile->getContent();
        }

        $template           = new \BackendTemplate('be_composer_client_editor');
        $template->composer = $this->composer;
        $template->config   = $config;
        return $template->parse();
    }
}
