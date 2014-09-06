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
use ContaoCommunityAlliance\Contao\Composer\Runtime;

/**
 * Class MigrationWizardController
 */
class MigrationWizardController extends AbstractController
{

    static protected $versionNames = array
    (
        0 => '-alpha',
        1 => '-alpha',
        2 => '-alpha',
        3 => '-beta',
        4 => '-beta',
        5 => '-beta',
        6 => '-RC',
        7 => '-RC',
        8 => '-RC',
        9 => ''
    );

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function handle(\Input $input)
    {
        if (\Database::getInstance()->tableExists('tl_repository_installs')) {
            $oldPackageCount    = \Database::getInstance()
                                           ->execute('SELECT COUNT(*) AS count FROM tl_repository_installs')
                ->count;
            $commercialPackages = \Database::getInstance()
                                           ->execute('SELECT * FROM tl_repository_installs WHERE lickey!=\'\'')
                                           ->fetchEach('extension');
            $commercialPackages = count($commercialPackages)
                ? implode(', ', $commercialPackages)
                : false;
        } else {
            $oldPackageCount    = 0;
            $commercialPackages = '';
        }

        $smhEnabled            = Runtime::isSafeModeHackEnabled();
        $allowUrlFopenEnabled  = ini_get('allow_url_fopen');
        $pharSupportEnabled    = false;
        $apcOpcodeCacheEnabled = ini_get('apc.enabled') && ini_get('apc.cache_by_default');

        try {
            if (class_exists('Phar', false)) {
                new \Phar(TL_ROOT . '/system/modules/!composer/config/test.phar');
                $pharSupportEnabled = true;
            }
        } catch (\Exception $e) {
        }

        $composerSupported = !$smhEnabled && $allowUrlFopenEnabled && $pharSupportEnabled;

        $gitAvailable = Runtime::testProcess('git --version');
        $hgAvailable  = Runtime::testProcess('hg --version');
        $svnAvailable = Runtime::testProcess('svn --version');

        $mode  = 'upgrade';
        $setup = 'production';

        if ($composerSupported && $input->post('FORM_SUBMIT') == 'tl_composer_migrate') {
            $target = 'contao/main.php?do=composer';

            $mode  = $input->post('mode');
            $setup = $input->post('setup');

            // load config
            $json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
            $config = $json->read();

            if ($input->post('skip')) {
                // mark migration skipped
                $config['extra']['contao']['migrated'] = 'skipped';

                $_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['migrationSkipped'];
            } else {
                if (\Database::getInstance()->tableExists('tl_repository_installs')) {
                    switch ($mode) {
                        case 'upgrade':
                            $this->removeER2Files();

                            $install = \Database::getInstance()
                                                ->query('SELECT * FROM tl_repository_installs WHERE lickey=""');
                            while ($install->next()) {
                                // skip the composer package
                                if ($install->extension == 'composer') {
                                    continue;
                                }

                                $packageName = 'contao-legacy/' . $install->extension;
                                /*
                                $packageName = preg_replace(
                                    '{(?:([a-z])([A-Z])|([A-Z])([A-Z][a-z]))}',
                                    '\\1\\3-\\2\\4',
                                    $packageName
                                );
                                */
                                $packageName = strtolower($packageName);

                                $oldVersion = $install->version;
                                $stability  = $oldVersion % 10;
                                $oldVersion = (int) ($oldVersion / 10);
                                $oldVersion = (int) ($oldVersion / 1000);
                                $minor      = $oldVersion % 1000;
                                $major      = (int) ($oldVersion / 1000);

                                $version = sprintf(
                                    '~%d.%d%s',
                                    $major,
                                    $minor,
                                    static::$versionNames[$stability]
                                );

                                $config['require'][$packageName] = $version;
                            }

                            $target = 'contao/main.php?do=composer&update=packages';
                            break;

                        case 'clean':
                            $this->removeER2Files();
                            break;
                    }
                }

                switch ($setup) {
                    case 'production':
                        $config['minimum-stability']           = 'dev';
                        $config['prefer-stable']               = true;
                        $config['config']['preferred-install'] = 'dist';
                        break;

                    case 'development':
                        $config['minimum-stability']           = 'dev';
                        $config['prefer-stable']               = true;
                        $config['config']['preferred-install'] = 'source';
                        break;
                }

                // mark migration done
                $config['extra']['contao']['migrated'] = 'done';

                $_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['migrationDone'];
            }

            // write config
            $json->write($config);

            $this->redirect($target);
        }

        $template                        = new \BackendTemplate('be_composer_client_migrate');
        $template->composer              = $this->composer;
        $template->smhEnabled            = $smhEnabled;
        $template->allowUrlFopenEnabled  = $allowUrlFopenEnabled;
        $template->pharSupportEnabled    = $pharSupportEnabled;
        $template->composerSupported     = $composerSupported;
        $template->apcOpcodeCacheEnabled = $apcOpcodeCacheEnabled;
        $template->oldPackageCount       = $oldPackageCount;
        $template->commercialPackages    = $commercialPackages;
        $template->gitAvailable          = $gitAvailable;
        $template->hgAvailable           = $hgAvailable;
        $template->svnAvailable          = $svnAvailable;
        $template->mode                  = $mode;
        $template->setup                 = $setup;
        return $template->parse();
    }

    /**
     * Remove all files installed with ER2 client
     */
    protected function removeER2Files()
    {
        $files      = \Files::getInstance();
        $file       = \Database::getInstance()
                               ->query(
                                   'SELECT f.*
				 FROM tl_repository_instfiles f
				 INNER JOIN tl_repository_installs i
				 ON i.id=f.pid
				 WHERE i.extension!="composer"
				 ORDER BY filetype="D", filetype="F", filename DESC'
                               );
        $fileIds    = array();
        $installIds = array();
        while ($file->next()) {
            $path = TL_ROOT . '/' . $file->filename;
            switch ($file->filetype) {
                case 'F':
                    if (file_exists($path)) {
                        $fileIds[]    = $file->id;
                        $installIds[] = $file->pid;
                        $files->delete($file->filename);
                    }
                    break;

                case 'D':
                    if (is_dir($path) && !count(scan($path))) {
                        $installIds[] = $file->pid;
                        $files->rmdir($file->filename);
                    }
                    break;
            }
        }
        if (count($installIds)) {
            \Database::getInstance()
                     ->query(
                         'UPDATE tl_repository_installs SET error=1 WHERE id IN (' . implode(
                             ',',
                             array_unique($installIds)
                         ) . ')'
                     );
        }
        if (count($fileIds)) {
            \Database::getInstance()
                     ->query(
                         'UPDATE tl_repository_instfiles SET flag="D" WHERE id IN (' . implode(',', $fileIds) . ')'
                     );
        }
    }
}
