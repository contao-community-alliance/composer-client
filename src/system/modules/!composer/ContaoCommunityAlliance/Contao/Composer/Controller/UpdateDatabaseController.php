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
 * Class UpdateDatabaseController
 */
class UpdateDatabaseController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $this->handleRunOnce(); // PATCH

        if ($input->post('FORM_SUBMIT') == 'database-update') {
            $count = 0;
            $sql   = deserialize($input->post('sql'));
            if (is_array($sql)) {
                foreach ($sql as $key) {
                    if (isset($_SESSION['sql_commands'][$key])) {
                        $this->Database->query(
                            str_replace(
                                'DEFAULT CHARSET=utf8;',
                                'DEFAULT CHARSET=utf8 COLLATE ' . $GLOBALS['TL_CONFIG']['dbCollation'] . ';',
                                $_SESSION['sql_commands'][$key]
                            )
                        );
                        $count++;
                    }
                }
            }
            $_SESSION['sql_commands'] = array();
            $_SESSION['TL_CONFIRM'][] = sprintf($GLOBALS['TL_LANG']['composer_client']['databaseUpdated'], $count);
            $this->reload();
        }

        if (version_compare(VERSION, '3', '>=')) {
            /** @var \Contao\Database\Installer $installer */
            $installer = \System::importStatic('Database\Installer');
        } else {
            $this->import('DbInstaller');
            /** @var \DbInstaller $installer */
            $installer = $this->DbInstaller;
        }

        $form = $installer->generateSqlForm();

        if (empty($_SESSION['sql_commands'])) {
            $_SESSION['TL_INFO'][] = $GLOBALS['TL_LANG']['composer_client']['databaseUptodate'];
            $this->redirect('contao/main.php?do=composer');
        }

        $form = preg_replace(
            '#(<label for="sql_\d+")>(CREATE TABLE)#',
            '$1 class="create_table">$2',
            $form
        );
        $form = preg_replace(
            '#(<label for="sql_\d+")>(ALTER TABLE `[^`]+` ADD)#',
            '$1 class="alter_add">$2',
            $form
        );
        $form = preg_replace(
            '#(<label for="sql_\d+")>(ALTER TABLE `[^`]+` DROP)#',
            '$1 class="alter_drop">$2',
            $form
        );
        $form = preg_replace(
            '#(<label for="sql_\d+")>(DROP TABLE)#',
            '$1 class="drop_table">$2',
            $form
        );

        $template           = new \BackendTemplate('be_composer_client_update');
        $template->composer = $this->composer;
        $template->form     = $form;
        return $template->parse();
    }
}
