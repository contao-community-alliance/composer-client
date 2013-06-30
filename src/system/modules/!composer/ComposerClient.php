<?php

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Console\HtmlOutputFormatter;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\PackageInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\ComposerRepository;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;
use Composer\DependencyResolver\Pool;
use Symfony\Component\Process\Process;

/**
 * Class ComposerClientBackend
 *
 * Composer client interface.
 */
class ComposerClient extends System
{
	/**
	 * @var ComposerClient
	 */
	protected static $instance;

	static public function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	protected $inactiveModulesOptionsCallback = null;

	public function setInactiveModulesOptionsCallback($inactiveModulesOptionsCallback)
	{
		$this->inactiveModulesOptionsCallback = $inactiveModulesOptionsCallback;
		return $this;
	}

	public function getInactiveModulesOptionsCallback()
	{
		return $this->inactiveModulesOptionsCallback;
	}

	public function getModules()
	{
		$callback = $this->inactiveModulesOptionsCallback;
		$this->import($callback[0]);
		$modules = $this->$callback[0]->$callback[1]();

		foreach (array('repository', 'rep_base', 'rep_client') as $module) {
			if (isset($modules[$module])) {
				$modules[$module] = sprintf(
					'<span style="text-decoration:line-through">%s</span> <span style="color:#f00">%s</span>',
					$modules[$module],
					$GLOBALS['TL_LANG']['MSG']['disabled_by_composer']
				);
			}
		}

		return $modules;
	}

	public function disableOldClientHook()
	{
		// disable the repo client
		$reset           = false;
		$activeModules   = $this->Config->getActiveModules();
		$inactiveModules = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);

		if (in_array('rep_base', $activeModules)) {
			$inactiveModules[] = 'rep_base';
			$reset             = true;
		}
		if (in_array('rep_client', $activeModules)) {
			$inactiveModules[] = 'rep_client';
			$reset             = true;
		}
		if (in_array('repository', $activeModules)) {
			$inactiveModules[] = 'repository';
			$skipFile          = new File('system/modules/repository/.skip');
			$skipFile->write('Remove this file to enable the module');
			$skipFile->close();
			$reset = true;
		}
		if ($reset) {
			$this->Config->update("\$GLOBALS['TL_CONFIG']['inactiveModules']", serialize($inactiveModules));
			$this->reload();
		}
		unset($GLOBALS['TL_HOOK']['loadLanguageFiles']['composer']);
	}
}
