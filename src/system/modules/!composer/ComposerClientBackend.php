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
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Repository\InstalledArrayRepository;
use Symfony\Component\Process\Process;

/**
 * Class ComposerClientBackend
 *
 * Composer client interface.
 */
class ComposerClientBackend extends BackendModule
{
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

		if ($input->get('update') == 'database') {
			$this->updateDatabase($input);
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

		if ($input->post('update') == 'packages') {
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
		$this->Template->output          = $_SESSION['COMPOSER_OUTPUT'];

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
				return !isset($dependencyMap[$localPackage->getName()]) && !($localPackage instanceof \Composer\Package\AliasPackage);
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
		$index = 0;

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
		$current = (object) array(
			'package'  => $package,
			'required' => (object) array(
				'from'       => $requiredFrom,
				'constraint' => $requiredConstraint,
				'parents'    => $parents,
			),
			'lastInLevel' => $isLast ? $parents - 1 : -1
		);
		$dependencyGraph[] = $current;

		$requires = $package->getRequires();
		$requiresCount = count($requires);
		$index = 0;
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
					'package'  => $requireName,
					'required' => (object) array(
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
						 'do' => 'composer',
						 'solve' => $packageName,
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
		$version = base64_decode(rawurldecode($input->get('version')));

		$versionParser = new VersionParser();
		$constraint = $versionParser->parseConstraints($version);

        $aliases = $this->getRootAliases($rootPackage);
        $this->aliasPlatformPackages($platformRepo, $aliases);

		$pool = $this->getPool($rootPackage->getStability(), $rootPackage->getStabilityFlags());
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
					if ($operation->getPackage()->getName() != $packageName &&
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
		$this->Template->packageName = $packageName;
		$this->Template->packageVersion  = $version;
		$this->Template->operations = $operations;
	}

	/**
	 * Search for a single packages versions.
	 *
	 * @param string   $packageName
	 *
	 * @return PackageInterface[]
	 */
	protected function searchPackage($packageName)
	{
		$rootPackage = $this->composer->getPackage();

		$pool = $this->getPool($rootPackage->getStability(), $rootPackage->getStabilityFlags());

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
			$this->reload();
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

			return $this->download($url, $file);
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

    private function getRootAliases(RootPackageInterface $rootPackage)
    {
		$aliases = $rootPackage->getAliases();

        $normalizedAliases = array();

        foreach ($aliases as $alias) {
            $normalizedAliases[$alias['package']][$alias['version']] = array(
                'alias' => $alias['alias'],
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
