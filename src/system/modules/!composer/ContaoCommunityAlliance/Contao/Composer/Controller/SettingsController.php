<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
use Composer\Config;
use Composer\Factory;
use Composer\Installer;
use Composer\Console\HtmlOutputFormatter;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Solver;
use Composer\DependencyResolver\Request;
use Composer\DependencyResolver\SolverProblemsException;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Repository\InstalledArrayRepository;
use ContaoCommunityAlliance\ComposerInstaller\ConfigUpdateException;
use ContaoCommunityAlliance\Contao\Composer\Controller\AbstractController;
use ContaoCommunityAlliance\Contao\Composer\Controller\ClearComposerCacheController;
use ContaoCommunityAlliance\Contao\Composer\Controller\MigrationWizardController;
use ContaoCommunityAlliance\Contao\Composer\Controller\SearchController;
use ContaoCommunityAlliance\Contao\Composer\Controller\UndoMigrationController;
use ContaoCommunityAlliance\Contao\Composer\Controller\UpdateDatabaseController;

/**
 * Class SettingsController
 */
class SettingsController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		/** @var RootPackage $rootPackage */
		$rootPackage = $this->composer->getPackage();
		/** @var Config $config */
		$config      = $this->composer->getConfig();

		$minimumStability = new \SelectMenu(
			array(
				'id'          => 'minimum-stability',
				'name'        => 'minimum-stability',
				'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability'][0],
				'description' => $GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability'][1],
				'options'     => array(
					array('value' => 'stable', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_stable']),
					array('value' => 'RC', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_rc']),
					array('value' => 'beta', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_beta']),
					array('value' => 'alpha', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_alpha']),
					array('value' => 'dev', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_dev']),
				),
				'value'       => $rootPackage->getMinimumStability(),
				'class'       => 'minimum-stability',
				'required'    => true
			)
		);
		$preferStable     = new \CheckBox(
			array(
				'id'          => 'prefer-stable',
				'name'        => 'prefer-stable',
				'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable'][0],
				'description' => $GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable'][1],
				'options'     => array(
					array(
						'value' => '1',
						'label' => $GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable'][0]
					),
				),
				'value'       => $rootPackage->getPreferStable(),
				'class'       => 'prefer-stable',
				'required'    => true
			)
		);
		$preferredInstall = new \SelectMenu(
			array(
				'id'          => 'preferred-install',
				'name'        => 'preferred-install',
				'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'][0],
				'description' => $GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'][1],
				'options'     => array(
					array('value' => 'source', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_source']),
					array('value' => 'dist', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_dist']),
					array('value' => 'auto', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_auto']),
				),
				'value'       => $config->get('preferred-install'),
				'class'       => 'preferred-install',
				'required'    => true
			)
		);

		if ($input->post('FORM_SUBMIT') == 'tl_composer_settings') {
			$doSave = false;
			$json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
			$config = $json->read();

			$minimumStability->validate();
			$preferStable->validate();
			$preferredInstall->validate();

			if (!$minimumStability->hasErrors()) {
				$config['minimum-stability'] = $minimumStability->value;
				$doSave                      = true;
			}

			if (!$preferStable->hasErrors()) {
				$config['prefer-stable'] = (bool) $preferStable->value;
				$doSave                  = true;
			}

			if (!$preferredInstall->hasErrors()) {
				$config['config']['preferred-install'] = $preferredInstall->value;
				$doSave                                = true;
			}

			if ($doSave) {
				// make a backup
				copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

				// update config file
				$json->write($config);
			}

			$this->redirect('contao/main.php?do=composer&settings=dialog');
		}

		$template                   = new \BackendTemplate('be_composer_client_settings');
		$template->composer         = $this->composer;
		$template->minimumStability = $minimumStability;
		$template->preferStable     = $preferStable;
		$template->preferredInstall = $preferredInstall;
		return $template->parse();
	}
}
