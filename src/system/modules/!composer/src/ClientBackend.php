<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @author     Dominik Zogg <dominik.zogg@gmail.com>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     sapeish <sapeish@gmail.com>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer;

use Composer\Composer;
use Composer\Console\HtmlOutputFormatter;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\BufferIO;
use Composer\Package\RootPackage;
use ContaoCommunityAlliance\Contao\Composer\Controller\ClearComposerCacheController;
use ContaoCommunityAlliance\Contao\Composer\Controller\DependencyGraphController;
use ContaoCommunityAlliance\Contao\Composer\Controller\DetachedController;
use ContaoCommunityAlliance\Contao\Composer\Controller\DetailsController;
use ContaoCommunityAlliance\Contao\Composer\Controller\ExpertsEditorController;
use ContaoCommunityAlliance\Contao\Composer\Controller\InstalledController;
use ContaoCommunityAlliance\Contao\Composer\Controller\MigrationWizardController;
use ContaoCommunityAlliance\Contao\Composer\Controller\PinController;
use ContaoCommunityAlliance\Contao\Composer\Controller\RemovePackageController;
use ContaoCommunityAlliance\Contao\Composer\Controller\ResyncController;
use ContaoCommunityAlliance\Contao\Composer\Controller\SearchController;
use ContaoCommunityAlliance\Contao\Composer\Controller\SettingsController;
use ContaoCommunityAlliance\Contao\Composer\Controller\SolveController;
use ContaoCommunityAlliance\Contao\Composer\Controller\ToolsController;
use ContaoCommunityAlliance\Contao\Composer\Controller\UpdateDatabaseController;
use ContaoCommunityAlliance\Contao\Composer\Controller\UpdatePackagesController;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ClientBackend
 *
 * Composer client interface.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ClientBackend extends \Backend
{

    /**
     * The pathname to the composer config file.
     *
     * @var string
     */
    protected $configPathname = null;

    /**
     * The io system.
     *
     * @var BufferIO
     */
    protected $io = null;

    /**
     * The composer instance.
     *
     * @var Composer
     */
    protected $composer = null;

    /**
     * Compile the current element
     */
    public function generate()
    {
        Runtime::setUp();

        $this->loadLanguageFile('composer_client');

        $input = \Input::getInstance();

        // check the environment
        $errors = Runtime::checkEnvironment();

        if ($errors !== true && count($errors)) {
            $template         = new \BackendTemplate('be_composer_client_errors');
            $template->errors = $errors;
            return $template->parse();
        }

        // check composer.phar is installed
        if (!file_exists(COMPOSER_DIR_ABSOULTE . '/composer.phar')) {
            // switch template
            $template = new \BackendTemplate('be_composer_client_install_composer');

            // do install composer library
            if ($input->post('install')) {
                $this->updateComposer();
                $this->reload();
            }

            return $template->parse();
        }

        if (file_exists(TL_ROOT . '/' . DetachedController::PID_FILE_PATHNAME)) {
            $controller = new DetachedController();
            $output     = $controller->handle($input);
            return $output;
        }

        // update composer.phar if requested
        if ($input->get('update') == 'composer') {
            $this->updateComposer();
            $this->redirect('contao/main.php?do=composer');
        }

        // load composer and the composer class loader
        $this->loadComposer();

        /** @var RootPackage $rootPackage */
        $rootPackage = $this->composer->getPackage();
        $extra       = $rootPackage->getExtra();

        $controller = null;

        // do migration
        if (!array_key_exists('contao', $extra)
            || !array_key_exists('migrated', $extra['contao'])
            || !$extra['contao']['migrated']
        ) {
            $controller = new MigrationWizardController();
        }

        // do update database
        if ($input->get('update') == 'database') {
            $controller = new UpdateDatabaseController();
        }

        // do clear composer cache
        if ($input->get('clear') == 'composer-cache') {
            $controller = new ClearComposerCacheController();
        }

        // show tools dialog
        if ($input->get('tools') == 'dialog') {
            $controller = new ToolsController();
        }

        // show resync tool
        if ($input->get('tools') == 'resync') {
            $controller = new ResyncController();
        }

        // show settings dialog
        if ($input->get('settings') == 'dialog') {
            $controller = new SettingsController();
        }

        // show experts editor
        if ($input->get('settings') == 'experts') {
            $controller = new ExpertsEditorController();
        }

        // show dependency graph
        if ($input->get('show') == 'dependency-graph') {
            $controller = new DependencyGraphController();
        }

        // do search
        if ($input->get('keyword')) {
            $controller = new SearchController();
        }

        // do install
        if ($input->get('install')) {
            $controller = new DetailsController();
        }

        // do solve
        if ($input->get('solve')) {
            $controller = new SolveController();
        }

        // do update packages
        if ($input->get('update') == 'packages' || $input->post('update') == 'packages') {
            $controller = new UpdatePackagesController();
        }

        // do pin/unpin package version
        if ($input->post('pin')) {
            $controller = new PinController();
        }

        // do remove package
        if ($input->post('remove')) {
            $controller = new RemovePackageController();
        }

        if (!$controller) {
            $controller = new InstalledController();
        }

        $controller->setConfigPathname($this->configPathname);
        $controller->setIo($this->io);
        $controller->setComposer($this->composer);
        $output = $controller->handle($input);

        chdir(TL_ROOT);

        return $output;
    }

    /**
     * Load and install the composer.phar.
     *
     * @return bool
     */
    protected function updateComposer()
    {
        try {
            Runtime::updateComposer();
            Messages::addConfirmation($GLOBALS['TL_LANG']['composer_client']['composerUpdated']);
            return true;
        } catch (\Exception $e) {
            $this->log(
                $e->getMessage() . "\n" . $e->getTraceAsString(),
                'ContaoCommunityAlliance\Contao\Composer\ClientBackend updateComposer',
                'TL_ERROR'
            );
            Messages::addError($e->getMessage());
            return false;
        }
    }

    /**
     * Return the proper debug level value.
     *
     * @return int
     */
    protected function getDebugLevel()
    {
        switch ($GLOBALS['TL_CONFIG']['composerVerbosity']) {
            case 'VERBOSITY_QUIET':
                return OutputInterface::VERBOSITY_QUIET;
            case 'VERBOSITY_VERBOSE':
                return OutputInterface::VERBOSITY_VERBOSE;
            case 'VERBOSITY_VERY_VERBOSE':
                return OutputInterface::VERBOSITY_VERY_VERBOSE;
            case 'VERBOSITY_DEBUG':
                return OutputInterface::VERBOSITY_DEBUG;
            case 'VERBOSITY_NORMAL':
            default:
        }

        return OutputInterface::VERBOSITY_NORMAL;
    }

    /**
     * Load composer and the composer class loader.
     */
    protected function loadComposer()
    {
        // search for composer build version
        $composerDevWarningTime = Runtime::readComposerDevWarningTime();
        $incompatibleVersion    = mktime(11, 0, 0, 6, 5, 2014) > ($composerDevWarningTime - 30 * 86400);

        if (!$composerDevWarningTime
            || $GLOBALS['TL_CONFIG']['composerAutoUpdateLibrary']
               && ($incompatibleVersion || time() > $composerDevWarningTime)
        ) {
            Runtime::updateComposer();
            Messages::addConfirmation($GLOBALS['TL_LANG']['composer_client']['composerUpdated']);
        }

        if ($composerDevWarningTime
            && !$GLOBALS['TL_CONFIG']['composerAutoUpdateLibrary']
            && $incompatibleVersion
        ) {
            Messages::addError($GLOBALS['TL_LANG']['composer_client']['composerUpdateNecessary']);
        }

        // register composer class loader
        Runtime::registerComposerClassLoader();

        // define pathname to config file
        $this->configPathname = COMPOSER_DIR_RELATIVE . '/' . Factory::getComposerFile();

        // create io interface
        $this->io = new BufferIO('', $this->getDebugLevel(), new HtmlOutputFormatter());

        // create composer
        $this->composer = Runtime::createComposer($this->io);
    }
}
