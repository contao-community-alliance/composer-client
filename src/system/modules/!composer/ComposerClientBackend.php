<?php

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Console\HtmlOutputFormatter;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\ComposerRepository;
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
use Composer\Util\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class ComposerClientBackend
 *
 * Composer client interface.
 */
class ComposerClientBackend extends BackendModule
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
	 * The template name
	 *
	 * @var string
	 */
	protected $strTemplate = 'be_composer_client';

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
	protected function compile()
	{
		$this->loadLanguageFile('composer_client');

		$input = Input::getInstance();

		// check the local environment
		if (!$this->checkEnvironment($input)) {
			return;
		}

		// load composer and the composer class loader
		$this->loadComposer();
		$extra = $this->composer
			->getPackage()
			->getExtra();

		if (!array_key_exists('contao', $extra) ||
			!array_key_exists('migrated', $extra['contao']) ||
			!$extra['contao']['migrated']
		) {
			$this->migrationWizard($input);
			return;
		}

		if ($input->get('migrate') == 'undo') {
			$this->undoMigration($input);
			return;
		}

		if ($input->get('update') == 'database') {
			$this->updateDatabase($input);
			return;
		}

		if ($input->get('clear') == 'composer-cache') {
			$this->clearComposerCache($input);
			return;
		}

		if ($input->get('settings') == 'dialog') {
			$this->showSettingsDialog($input);
			return;
		}

		if ($input->get('settings') == 'experts') {
			$this->showExpertsEditor($input);
			return;
		}

		if ($input->get('show') == 'dependency-graph') {
			$this->showDependencyGraph($input);
			return;
		}

		// do search
		if ($input->get('keyword')) {
			$this->doSearch($input);
			return;
		}

		// do install
		if ($input->get('install')) {
			$this->showDetails($input);
			return;
		}

		// do solve
		if ($input->get('solve')) {
			$this->solveDependencies($input);
			return;
		}

		if ($input->get('update') == 'packages' || $input->post('update') == 'packages') {
			$this->updatePackages();
			return;
		}

		/**
		 * Remove package
		 */
		if ($input->post('remove')) {
			$this->removePackage($input);
			$this->redirect('contao/main.php?do=composer');
		}

		// update contao version if needed
		$this->checkContaoVersion();

		// calculate dependency graph
		$dependencyMap = $this->calculateDependencyMap(
			$this->composer
				->getRepositoryManager()
				->getLocalRepository()
		);

		$this->Template->dependencyMap = $dependencyMap;
		$this->Template->output        = $_SESSION['COMPOSER_OUTPUT'];

		unset($_SESSION['COMPOSER_OUTPUT']);

		chdir(TL_ROOT);
	}

	/**
	 * Check the local environment, return false if there are problems.
	 *
	 * @param \Input $input
	 *
	 * @return bool
	 */
	protected function checkEnvironment(Input $input)
	{
		$errors = array();

		if ($GLOBALS['TL_CONFIG']['useFTP']) {
			$errors[] = $GLOBALS['TL_LANG']['composer_client']['ftp_mode'];
		}

		// check for php version
		if (version_compare(PHP_VERSION, '5.3.4', '<')) {
			$errors[] = sprintf($GLOBALS['TL_LANG']['composer_client']['php_version'], PHP_VERSION);
		}

		// check for curl
		if (!function_exists('curl_init')) {
			$errors[] = $GLOBALS['TL_LANG']['composer_client']['curl_missing'];
		}

		if (count($errors)) {
			$this->Template->setName('be_composer_client_errors');
			$this->Template->errors = $errors;
			return false;
		}

		/*
		 * Use composer.phar only, if composer is not installed locally
		 */
		if (!file_exists(TL_ROOT . '/composer/vendor/composer/composer/src/Composer/Composer.php') ||
			!file_exists(TL_ROOT . '/composer/vendor/autoload.php')
		) {
			if (!file_exists(TL_ROOT . '/composer/composer.phar')) {
				// switch template
				$this->Template->setName('be_composer_client_install_composer');

				// do install composer library
				if ($input->post('install')) {
					$this->updateComposer();
					$this->reload();
				}

				return false;
			}

			if ($input->get('update') == 'composer') {
				$this->updateComposer();
				$this->redirect('contao/main.php?do=composer');
			}
		}

		return true;
	}

	/**
	 * Load and install the composer.phar.
	 *
	 * @return bool
	 */
	protected function updateComposer()
	{
		$url = 'https://getcomposer.org/composer.phar';

		try {
			$this->download($url, TL_ROOT . '/composer/composer.phar');
			$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['composerUpdated'];
			return true;
		}
		catch (Exception $e) {
			$this->log($e->getMessage() . "\n" . $e->getTraceAsString(), 'ComposerClient updateComposer', 'TL_ERROR');
			$_SESSION['TL_ERROR'][] = $e->getMessage();
			return false;
		}
	}

	/**
	 * Load composer and the composer class loader.
	 */
	protected function loadComposer()
	{
		if (function_exists('apc_clear_cache') && !in_array('ini_set', explode(',', ini_get('disable_functions')))) {
			apc_clear_cache();
			$this->Template->apcDisabledByUs = ini_set('apc.cache_by_default', 0);
		}

		chdir(TL_ROOT . '/composer');

		// unregister contao class loader
		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_unregister('__autoload');
		}

		// try to increase memory limit
		$this->increaseMemoryLimit();

		// register composer class loader
		if (file_exists(TL_ROOT . '/composer/vendor/composer/composer/src/Composer/Composer.php') &&
			file_exists(TL_ROOT . '/composer/vendor/autoload.php')
		) {
			require_once(TL_ROOT . '/composer/vendor/autoload.php');
		}

		// register composer class loader from phar
		if (file_exists(TL_ROOT . '/composer/composer.phar')) {
			$phar             = new Phar(TL_ROOT . '/composer/composer.phar');
			$autoloadPathname = $phar['vendor/autoload.php'];
			require_once($autoloadPathname->getPathname());

		}

		// reregister contao class loader
		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_register('__autoload');
		}

		// search for composer build version
		if (file_exists(TL_ROOT . '/composer/composer.phar')) {
			$composerDevWarningTime = $this->readComposerDevWarningTime();
			if (!$composerDevWarningTime || time() > $composerDevWarningTime) {
				$_SESSION['TL_ERROR'][]         = $GLOBALS['TL_LANG']['composer_client']['composerUpdateRequired'];
				$this->Template->composerUpdate = true;
			}
		}

		// define pathname to config file
		$this->configPathname = 'composer/' . Factory::getComposerFile();

		// create io interace
		$this->io = new BufferIO('', null, new HtmlOutputFormatter());

		// create composer factory
		/** @var \Composer\Factory $factory */
		$factory = new Factory();

		// create composer
		$this->composer = $factory->createComposer($this->io);

		// assign composer to template
		$this->Template->composer = $this->composer;
	}

	/**
	 * Try to increase memory.
	 */
	protected function increaseMemoryLimit()
	{
		/**
		 * Copyright (c) 2011 Nils Adermann, Jordi Boggiano
		 *
		 * @see https://github.com/composer/composer/blob/master/bin/composer
		 */
		if (function_exists('ini_set')) {
			@ini_set('display_errors', 1);

			$memoryInBytes = function ($value) {
				$unit  = strtolower(substr($value, -1, 1));
				$value = (int) $value;
				switch ($unit) {
					case 'g':
						$value *= 1024;
					// no break (cumulative multiplier)
					case 'm':
						$value *= 1024;
					// no break (cumulative multiplier)
					case 'k':
						$value *= 1024;
				}

				return $value;
			};

			$memoryLimit = trim(ini_get('memory_limit'));
			// Increase memory_limit if it is lower than 512M
			if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 512 * 1024 * 1024) {
				@ini_set('memory_limit', '512M');
			}
			unset($memoryInBytes, $memoryLimit);
		}
	}

	/**
	 * Read the stub from the composer.phar and return the warning timestamp.
	 *
	 * @return bool|int
	 */
	protected function readComposerDevWarningTime()
	{
		$configPathname = new File('composer/composer.phar');
		$buffer         = '';
		do {
			$buffer .= fread($configPathname->handle, 1024);
		} while (!preg_match('#define\(\'COMPOSER_DEV_WARNING_TIME\',\s*(\d+)\);#', $buffer, $matches) && !feof(
				$configPathname->handle
			));
		if ($matches[1]) {
			return (int) $matches[1];
		}
		return false;
	}

	/**
	 * Migration wizard
	 */
	protected function migrationWizard(Input $input)
	{
		$oldPackageCount    = Database::getInstance()
			->execute('SELECT COUNT(*) AS count FROM tl_repository_installs')
			->count;
		$commercialPackages = Database::getInstance()
			->execute('SELECT * FROM tl_repository_installs WHERE lickey!=\'\'')
			->fetchEach('extension');
		$commercialPackages = count($commercialPackages)
			? implode(', ', $commercialPackages)
			: false;

		$smhEnabled            = $GLOBALS['TL_CONFIG']['useFTP'];
		$allowUrlFopenEnabled  = ini_get('allow_url_fopen');
		$pharSupportEnabled    = false;
		$apcOpcodeCacheEnabled = ini_get('apc.enabled') && ini_get('apc.cache_by_default');

		try {
			if (class_exists('Phar', false)) {
				new Phar(TL_ROOT . '/system/modules/!composer/config/test.phar');
				$pharSupportEnabled = true;
			}
		}
		catch (Exception $e) {
		}

		$composerSupported = !$smhEnabled && $allowUrlFopenEnabled && $pharSupportEnabled;

		$gitAvailable = $this->testProc('git --version');
		$hgAvailable  = $this->testProc('hg --version');
		$svnAvailable = $this->testProc('svn --version');

		$mode  = 'upgrade';
		$setup = $gitAvailable ? 'production_extended' : 'production_compat';

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
			}
			else {
				switch ($mode) {
					case 'upgrade':
						$this->removeER2Files();

						$install = Database::getInstance()
							->query('SELECT * FROM tl_repository_installs WHERE lickey=""');
						while ($install->next()) {
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
							$build      = $install->build;
							$stability  = $oldVersion % 10;
							$oldVersion = (int) ($oldVersion / 10);
							$release    = $oldVersion % 1000;
							$oldVersion = (int) ($oldVersion / 1000);
							$minor      = $oldVersion % 1000;
							$major      = (int) ($oldVersion / 1000);

							$version = sprintf(
								'%d.%d.%d.%d%s',
								$major,
								$minor,
								$release,
								($stability * 1000 + $build),
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

				switch ($setup) {
					case 'production_compat':
						$config['minimum-stability']           = 'stable';
						$config['prefer-stable']               = true;
						$config['config']['preferred-install'] = 'dist';
						break;

					case 'production_extended':
						$config['minimum-stability']           = 'stable';
						$config['prefer-stable']               = true;
						$config['config']['preferred-install'] = 'source';
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

		$this->Template->setName('be_composer_client_migrate');
		$this->Template->smhEnabled            = $smhEnabled;
		$this->Template->allowUrlFopenEnabled  = $allowUrlFopenEnabled;
		$this->Template->pharSupportEnabled    = $pharSupportEnabled;
		$this->Template->composerSupported     = $composerSupported;
		$this->Template->apcOpcodeCacheEnabled = $apcOpcodeCacheEnabled;
		$this->Template->oldPackageCount       = $oldPackageCount;
		$this->Template->commercialPackages    = $commercialPackages;
		$this->Template->gitAvailable          = $gitAvailable;
		$this->Template->hgAvailable           = $hgAvailable;
		$this->Template->svnAvailable          = $svnAvailable;
		$this->Template->mode                  = $mode;
		$this->Template->setup                 = $setup;
	}

	/**
	 * Undo migration
	 */
	protected function undoMigration(Input $input)
	{
		if ($input->post('FORM_SUBMIT') == 'tl_composer_migrate_undo') {
			$requires = $this->composer
				->getPackage()
				->getRequires();
			foreach ($requires as $package => $constraint) {
				if ($package != 'contao-community-alliance/composer') {
					unset($requires[$package]);
				}
			}
			$this->composer
				->getPackage()
				->setRequires($requires);

			$lockPathname = preg_replace('#\.json$#', '.lock', $this->configPathname);

			$this->composer
				->getDownloadManager()
				->setOutputProgress(false);
			$installer = Installer::create($this->io, $this->composer);

			if (file_exists(TL_ROOT . '/' . $lockPathname)) {
				$installer->setUpdate(true);
			}

			if ($installer->run()) {
				$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
			}
			else {
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
				$skipFile = new File('system/modules/!composer/.skip');
				$skipFile->write('Remove this file to enable the module');
				$skipFile->close();
			}
			if (file_exists(TL_ROOT . '/system/modules/repository/.skip')) {
				$skipFile = new File('system/modules/repository/.skip');
				$skipFile->delete();
			}
			$this->Config->update("\$GLOBALS['TL_CONFIG']['inactiveModules']", serialize($inactiveModules));

			$this->redirect('contao/main.php?do=repository_manager');
		}

		$this->Template->setName('be_composer_client_migrate_undo');
		$this->Template->output = $_SESSION['COMPOSER_OUTPUT'];

		unset($_SESSION['COMPOSER_OUTPUT']);
	}

	/**
	 * Remove all files installed with ER2 client
	 */
	protected function removeER2Files()
	{
		$files      = Files::getInstance();
		$file       = Database::getInstance()
			->query('SELECT * FROM tl_repository_instfiles ORDER BY filetype="D", filetype="F", filename DESC');
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
			Database::getInstance()
				->query(
					'UPDATE tl_repository_installs SET error=1 WHERE id IN (' . implode(
						',',
						array_unique($installIds)
					) . ')'
				);
		}
		if (count($fileIds)) {
			Database::getInstance()
				->query('UPDATE tl_repository_instfiles SET flag="D" WHERE id IN (' . implode(',', $fileIds) . ')');
		}
	}

	/**
	 * Update the database scheme
	 */
	protected function updateDatabase(Input $input)
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
			$installer = System::importStatic('Database\Installer');
		}
		else {
			$this->import('DbInstaller');
			/** @var \DbInstaller $installer */
			$installer = $this->DbInstaller;
		}

		$form = $installer->generateSqlForm();

		if (empty($_SESSION['sql_commands'])) {
			$_SESSION['TL_INFO'][] = $GLOBALS['TL_LANG']['composer_client']['databaseUptodate'];
			$this->redirect('contao/main.php?do=composer');
		}

		$this->Template->setName('be_composer_client_update');
		$this->Template->form = $form;
	}

	/**
	 * Clear composer cache.
	 *
	 * @param \Input $input
	 */
	protected function clearComposerCache(Input $input)
	{
		if (is_dir(TL_ROOT . '/composer/cache')) {
			$fs = new Filesystem();
			$fs->removeDirectory(TL_ROOT . '/composer/cache');

			$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['composerCacheCleared'];
		}

		$this->redirect('contao/main.php?do=composer');
	}

	/**
	 * Show the settings dialog.
	 *
	 * @param \Input $input
	 */
	protected function showSettingsDialog(Input $input)
	{
		$rootPackage = $this->composer->getPackage();
		$config      = $this->composer->getConfig();

		$minimumStability = new SelectMenu(
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
		$preferStable     = new CheckBox(
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
		$preferredInstall = new SelectMenu(
			array(
				 'id'          => 'preferred-install',
				 'name'        => 'preferred-install',
				 'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'][0],
				 'description' => $GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'][1],
				 'options'     => array(
					 array('value' => 'sources', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_source']),
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

		$this->Template->setName('be_composer_client_settings');
		$this->Template->minimumStability = $minimumStability;
		$this->Template->preferStable     = $preferStable;
		$this->Template->preferredInstall = $preferredInstall;
	}

	/**
	 * Show the experts editor and handle updates.
	 *
	 * @param \Input $input
	 */
	protected function showExpertsEditor(Input $input)
	{
		$configFile = new File($this->configPathname);

		if ($input->post('save')) {
			$tempPathname = $this->configPathname . '~';
			$tempFile     = new File($tempPathname);

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
			}
			else {
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
		}
		else {
			$config = $configFile->getContent();
		}
		$this->Template->setName('be_composer_client_editor');
		$this->Template->config = $config;
	}

	/**
	 * Show graph of dependencies.
	 *
	 * @param \Input $input
	 */
	protected function showDependencyGraph(Input $input)
	{
		$localRepository = $this->composer
			->getRepositoryManager()
			->getLocalRepository();

		$dependencyMap = $this->calculateDependencyMap($localRepository);

		$dependencyGraph = array();

		$localPackages = $localRepository->getPackages();

		$localPackages = array_filter(
			$localPackages,
			function ($localPackage) use ($dependencyMap) {
				return !isset($dependencyMap[$localPackage->getName(
				)]) && !($localPackage instanceof \Composer\Package\AliasPackage);
			}
		);

		$allLocalPackages = $localRepository->getPackages();
		$allLocalPackages = array_combine(
			array_map(
				function ($localPackage) {
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

		$this->Template->setName('be_composer_client_dependency_graph');
		$this->Template->dependencyGraph = $dependencyGraph;
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
		$parents = 0
	) {
		$current           = (object) array(
			'package'     => $package,
			'required'    => (object) array(
				'from'       => $requiredFrom,
				'constraint' => $requiredConstraint,
				'parents'    => $parents,
			),
			'lastInLevel' => $isLast ? $parents - 1 : -1
		);
		$dependencyGraph[] = $current;

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
					$parents + 1
				);
			}
			else {
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


	/**
	 * Do a package search.
	 *
	 * @param Input $input
	 */
	protected function doSearch(Input $input)
	{
		$keyword = $input->get('keyword');

		$tokens = explode(' ', $keyword);
		$tokens = array_map('trim', $tokens);
		$tokens = array_filter($tokens);

		$searchName = count($tokens) == 1 && strpos($tokens[0], '/') !== false;

		if (empty($tokens)) {
			$_SESSION['COMPOSER_OUTPUT'] = $this->io->getOutput();
			$this->redirect('contao/main.php?do=composer');
		}

		$packages = $this->searchPackages(
			$tokens,
			$searchName ? RepositoryInterface::SEARCH_NAME : RepositoryInterface::SEARCH_FULLTEXT
		);

		if (empty($packages)) {
			$_SESSION['TL_ERROR'][] = sprintf(
				$GLOBALS['TL_LANG']['composer_client']['noSearchResult'],
				$keyword
			);

			$_SESSION['COMPOSER_OUTPUT'] = $this->io->getOutput();
			$this->redirect('contao/main.php?do=composer');
		}

		$this->Template->setName('be_composer_client_search');
		$this->Template->keyword  = $keyword;
		$this->Template->packages = $packages;
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
		$platformRepo        = new PlatformRepository;
		$localRepository     = $this->composer
			->getRepositoryManager()
			->getLocalRepository();
		$installedRepository = new CompositeRepository(
			array($localRepository, $platformRepo)
		);
		$repositories        = new CompositeRepository(
			array_merge(
				array($installedRepository),
				$this->composer
					->getRepositoryManager()
					->getRepositories()
			)
		);

		/*
		$localRepository       = $this->composer
			->getRepositoryManager()
			->getLocalRepository();
		$platformRepository    = new PlatformRepository();
		$installedRepositories = new CompositeRepository(
			array(
				$localRepository,
				$platformRepository
			)
		);
		$repositories          = array_merge(
			array($installedRepositories),
			$this->composer
				->getRepositoryManager()
				->getRepositories()
		);

		$repositories = new CompositeRepository($repositories);
		*/

		$results = $repositories->search(implode(' ', $tokens), $searchIn);

		$packages = array();
		foreach ($results as $result) {
			if (!isset($packages[$result['name']])) {
				$packages[$result['name']] = $result;
			}
		}

		return $packages;
	}

	/**
	 * Show package details.
	 *
	 * @param Input $input
	 */
	protected function showDetails(Input $input)
	{
		$packageName = $input->get('install');

		if ($input->post('version')) {
			$version = $input->post('version');

			$this->redirect(
				'contao/main.php?' . http_build_query(
					array(
						 'do'      => 'composer',
						 'solve'   => $packageName,
						 'version' => $version
					)
				)
			);
		}

		$installationCandidates = $this->searchPackage($packageName);

		if (empty($installationCandidates)) {
			$_SESSION['TL_ERROR'][] = sprintf(
				$GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates'],
				$packageName
			);

			$_SESSION['COMPOSER_OUTPUT'] = $this->io->getOutput();
			$this->redirect('contao/main.php?do=composer');
		}

		$this->Template->setName('be_composer_client_install');
		$this->Template->packageName = $packageName;
		$this->Template->candidates  = $installationCandidates;
	}

	/**
	 * Solve package dependencies.
	 *
	 * @param Input $input
	 */
	protected function solveDependencies(Input $input)
	{
		$rootPackage = $this->composer->getPackage();

		$installedRootPackage = clone $rootPackage;
		$installedRootPackage->setRequires(array());
		$installedRootPackage->setDevRequires(array());

		$localRepository     = $this->composer
			->getRepositoryManager()
			->getLocalRepository();
		$platformRepo        = new PlatformRepository;
		$installedRepository = new CompositeRepository(
			array(
				 $localRepository,
				 new InstalledArrayRepository(array($installedRootPackage)),
				 $platformRepo
			)
		);

		$packageName = $input->get('solve');
		$version     = base64_decode(rawurldecode($input->get('version')));

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
		$rootPackageConstraint = new VersionConstraint('=', $rootPackage->getVersion());
		$rootPackageConstraint->setPrettyString($rootPackage->getPrettyVersion());
		$request->install($rootPackage->getName(), $rootPackageConstraint);

		// add requirements
		$links = $rootPackage->getRequires();
		foreach ($links as $link) {
			if ($link->getTarget() != $packageName) {
				$request->install($link->getTarget(), $link->getConstraint());
			}
		}
		foreach ($installedRepository->getPackages() as $package) {
			$request->install($package->getName(), new VersionConstraint('=', $package->getVersion()));
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
							->getName() != $packageName &&
						$beforeOperation->__toString() == $operation->__toString()
					) {
						unset($operations[$index]);
					}
				}
			}

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

				$_SESSION['TL_INFO'][] = sprintf(
					$GLOBALS['TL_LANG']['composer_client']['added_candidate'],
					$packageName,
					$version
				);

				$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

				if ($input->post('install')) {
					$this->redirect('contao/main.php?do=composer&update=packages');
				}
				$this->redirect('contao/main.php?do=composer');
			}
		}
		catch (SolverProblemsException $e) {
			$_SESSION['TL_ERROR'][] = sprintf(
				'<span style="white-space: pre-line">%s</span>',
				trim($e->getMessage())
			);
		}

		$this->Template->setName('be_composer_client_solve');
		$this->Template->packageName    = $packageName;
		$this->Template->packageVersion = $version;
		$this->Template->operations     = $operations;
	}

	/**
	 * Search for a single packages versions.
	 *
	 * @param string $packageName
	 *
	 * @return PackageInterface[]
	 */
	protected function searchPackage($packageName)
	{
		$rootPackage = $this->composer->getPackage();

		$pool = $this->getPool();

		$versions = array();
		$seen     = array();
		$matches  = $pool->whatProvides($packageName);
		foreach ($matches as $package) {
			// skip providers/replacers
			if ($package->getName() !== $packageName) {
				continue;
			}
			// add each version only once to skip installed version.
			if (!in_array($package->getPrettyVersion(), $seen)) {
				$seen[]     = $package->getPrettyVersion();
				$versions[] = $package;
			}
		}

		usort(
			$versions,
			function (PackageInterface $packageA, PackageInterface $packageB) {
				// is this a wise idea?
				if (($dsa = $packageA->getReleaseDate()) && ($dsb = $packageB->getReleaseDate())) {
					return $dsb->getTimestamp() - $dsa->getTimestamp();
				}

				/*
				$versionA = $this->reformatVersion($packageA);
				$versionB = $this->reformatVersion($packageB);

				$classicA = preg_match('#^\d(\.\d+)*$#', $versionA);
				$classicB = preg_match('#^\d(\.\d+)*$#', $versionB);

				$branchA = 'dev-' == substr($packageA->getPrettyVersion(), 0, 4);
				$branchB = 'dev-' == substr($packageB->getPrettyVersion(), 0, 4);

				if ($branchA && $branchB) {
					return strcasecmp($branchA, $branchB);
				}
				if ($classicA && $classicB) {
					if ($packageA->getPrettyVersion() == 'dev-master') {
						return -1;
					}
					if ($packageB->getPrettyVersion() == 'dev-master') {
						return 1;
					}
					return version_compare($versionB, $versionA);
				}
				if ($classicA) {
					return -1;
				}
				if ($classicB) {
					return 1;
				}
				return 0;
				*/
			}
		);

		return $versions;
	}

	/**
	 * Run the package update process.
	 */
	protected function updatePackages()
	{
		try {
			if (version_compare(VERSION, '3', '<')) {
				spl_autoload_unregister('__autoload');
			}

			$lockPathname = preg_replace('#\.json$#', '.lock', $this->configPathname);

			$this->composer
				->getDownloadManager()
				->setOutputProgress(false);
			$installer = Installer::create($this->io, $this->composer);

			if (file_exists(TL_ROOT . '/' . $lockPathname)) {
				$installer->setUpdate(true);
			}

			if ($installer->run()) {
				$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

				// redirect to database update
				$this->redirect('contao/main.php?do=composer&update=database');
			}
			else {
				$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

				$this->redirect('contao/main.php?do=composer');
			}
		}
		catch (RuntimeException $e) {
			$_SESSION['TL_ERROR'][] = str_replace(TL_ROOT, '', $e->getMessage());
			$this->redirect('contao/main.php?do=composer');
		}
	}

	/**
	 * Remove a package from the requires list.
	 *
	 * @param Input $input
	 */
	protected function removePackage(Input $input)
	{
		$removeName = $input->post('remove');

		// make a backup
		copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

		// update requires
		$json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
		$config = $json->read();
		if (!array_key_exists('require', $config)) {
			$config['require'] = array();
		}
		unset($config['require'][$removeName]);
		$json->write($config);

		$_SESSION['TL_INFO'][] = sprintf(
			$GLOBALS['TL_LANG']['composer_client']['removeCandidate'],
			$removeName
		);

		$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
	}

	/**
	 * Check the contao version in the config file and update if necessary.
	 */
	protected function checkContaoVersion()
	{
		/** @var \Composer\Package\RootPackage $package */
		$package       = $this->composer->getPackage();
		$versionParser = new VersionParser();
		$version       = VERSION . (is_numeric(BUILD) ? '.' . BUILD : '-' . BUILD);
		$prettyVersion = $versionParser->normalize($version);
		if ($package->getVersion() !== $prettyVersion) {
			$configFile            = new JsonFile(TL_ROOT . '/' . $this->configPathname);
			$configJson            = $configFile->read();
			$configJson['version'] = $version;
			$configFile->write($configJson);

			$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
			$this->reload();
		}
	}

	/**
	 * Build dependency graph of installed packages.
	 *
	 * @param RepositoryInterface $repository
	 *
	 * @return array
	 */
	protected function calculateDependencyMap(RepositoryInterface $repository, $inverted = false)
	{
		$dependencyMap = array();

		/** @var \Composer\Package\PackageInterface $package */
		foreach ($repository->getPackages() as $package) {
			$this->fillDependencyMap($repository, $package, $dependencyMap, $inverted);
		}

		return $dependencyMap;
	}

	/**
	 * Fill the dependency graph with installed packages.
	 *
	 * @param RepositoryInterface $repository
	 * @param PackageInterface    $package
	 * @param array               $dependencyMap
	 */
	protected function fillDependencyMap(
		RepositoryInterface $repository,
		PackageInterface $package,
		array &$dependencyMap,
		$inverted
	) {
		/** @var string $requireName */
		/** @var \Composer\Package\Link $requireLink */
		foreach ($package->getRequires() as $requireName => $requireLink) {
			if ($inverted) {
				$dependencyMap[$package->getName()][$requireLink->getTarget()] = $requireLink->getPrettyConstraint();
			}
			else {
				$dependencyMap[$requireLink->getTarget()][$package->getName()] = $requireLink->getPrettyConstraint();
			}
		}
	}

	/**
	 * Download an url and return or store contents.
	 *
	 * @param string $url
	 * @param bool   $file
	 *
	 * @return bool|null|string
	 * @throws Exception
	 */
	protected function download($url, $file = false)
	{
		if (ini_get('allow_url_fopen')) {
			return $this->fgetDownload($url, $file);
		}
		elseif (function_exists('curl_init')) {
			return $this->curlDownload($url, $file);
		}
	}

	/**
	 * @param      $url
	 * @param bool $file
	 *
	 * @return bool|null|string
	 * @throws Exception
	 */
	protected function fgetDownload($url, $file = false)
	{
		$return = null;

		if ($file === false) {
			$return = true;
			$file   = 'php://temp';
		}

		$fileStream = fopen($file, 'wb+');

		fwrite($fileStream, file_get_contents($url));
		$headers              = $http_response_header;
		$firstHeaderLine      = $headers[0];
		$firstHeaderLineParts = explode(' ', $firstHeaderLine);

		if ($firstHeaderLineParts[1] == 301 || $firstHeaderLineParts[1] == 302) {
			foreach ($headers as $header) {
				$matches = array();
				preg_match('/^Location:(.*?)$/', $header, $matches);
				$url = trim(array_pop($matches));
				return $this->fgetDownload($url, $file);
			}
			throw new \Exception("Can't get the redirect location");
		}

		if ($return) {
			rewind($fileStream);
			$return = stream_get_contents($fileStream);
		}

		fclose($fileStream);

		return $return;
	}

	/**
	 * @param      $url
	 * @param bool $file
	 *
	 * @return bool|null|string
	 * @throws Exception
	 */
	protected function curlDownload($url, $file = false)
	{
		$return = null;

		if ($file === false) {
			$return = true;
			$file   = 'php://temp';
		}

		$curl = curl_init($url);

		$headerStream = fopen('php://temp', 'wb+');
		$fileStream   = fopen($file, 'wb+');

		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curl, CURLOPT_WRITEHEADER, $headerStream);
		curl_setopt($curl, CURLOPT_FILE, $fileStream);

		curl_exec($curl);

		rewind($headerStream);
		$header = stream_get_contents($headerStream);

		if ($return) {
			rewind($fileStream);
			$return = stream_get_contents($fileStream);
		}

		fclose($headerStream);
		fclose($fileStream);

		if (curl_errno($curl)) {
			throw new Exception(
				curl_error($curl),
				curl_errno($curl)
			);
		}

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code == 301 || $code == 302) {
			preg_match('/Location:(.*?)\n/', $header, $matches);
			$url = trim(array_pop($matches));

			return $this->curlDownload($url, $file);
		}

		return $return;
	}

	protected function getPool($minimumStability = 'dev', $stabilityFlags = array())
	{
		$platformRepo        = new PlatformRepository;
		$localRepository     = $this->composer
			->getRepositoryManager()
			->getLocalRepository();
		$installedRepository = new CompositeRepository(
			array($localRepository, $platformRepo)
		);
		$repositories        = new CompositeRepository(
			array_merge(
				array($installedRepository),
				$this->composer
					->getRepositoryManager()
					->getRepositories()
			)
		);

		$pool = new Pool($minimumStability, $stabilityFlags);
		$pool->addRepository($repositories);

		return $pool;
	}

	private function testProc($cmd)
	{
		$proc = proc_open(
			$cmd,
			array(
				 array('pipe', 'r'),
				 array('pipe', 'w'),
				 array('pipe', 'w')
			),
			$pipes
		);

		if (is_resource($proc)) {
			return !proc_close($proc);
		}

		return false;
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
