<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Downloader\DownloadManager;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\RootPackage;

/**
 * Class UndoMigrationController
 */
class UndoMigrationController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        if ($input->post('FORM_SUBMIT') == 'tl_composer_migrate_undo') {
            /** @var RootPackage $rootPackage */
            $rootPackage = $this->composer->getPackage();

            $requires = $rootPackage->getRequires();
            foreach (array_keys($requires) as $package) {
                if ($package != 'contao-community-alliance/composer') {
                    unset($requires[$package]);
                }
            }
            $rootPackage->setRequires($requires);

            $lockPathname = preg_replace('#\.json$#', '.lock', $this->configPathname);

            /** @var DownloadManager $downloadManager */
            $downloadManager = $this->composer->getDownloadManager();
            $downloadManager->setOutputProgress(false);

            $installer = Installer::create($this->io, $this->composer);

            if (file_exists(TL_ROOT . '/' . $lockPathname)) {
                $installer->setUpdate(true);
            }

            if ($installer->run()) {
                $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
            } else {
                $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

                $this->redirect('contao/main.php?do=composer&migrate=undo');
            }

            // load config
            $json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
            $config = $json->read();

            // remove migration status
            unset($config['extra']['contao']['migrated']);

            // write config
            $json->write($config);

            // disable composer client and enable repository client
            $inactiveModules   = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);
            $inactiveModules[] = '!composer';
            foreach (array('rep_base', 'rep_client', 'repository') as $module) {
                $pos = array_search($module, $inactiveModules);
                if ($pos !== false) {
                    unset($inactiveModules[$pos]);
                }
            }
            if (version_compare(VERSION, '3', '>=')) {
                $skipFile = new \File('system/modules/!composer/.skip');
                $skipFile->write('Remove this file to enable the module');
                $skipFile->close();
            }
            if (file_exists(TL_ROOT . '/system/modules/repository/.skip')) {
                $skipFile = new \File('system/modules/repository/.skip');
                $skipFile->delete();
            }
            $this->Config->update("\$GLOBALS['TL_CONFIG']['inactiveModules']", serialize($inactiveModules));

            $this->redirect('contao/main.php?do=repository_manager');
        }

        $template           = new \BackendTemplate('be_composer_client_migrate_undo');
        $template->composer = $this->composer;
        $template->output   = $_SESSION['COMPOSER_OUTPUT'];

        unset($_SESSION['COMPOSER_OUTPUT']);

        return $template->parse();
    }
}
