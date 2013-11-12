<?php

namespace ContaoCommunityAlliance\Contao\Composer;

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

class TemplateFunctions
{
	static public function isRemoveable($name, array $required, array $replaces)
	{
		if ($name == 'contao-community-alliance/composer') {
			return false;
		}

		if (isset($required[$name])) {
			return true;
		}

		if (isset($replaces[$name]) && $replaces[$name] != $name) {
			return static::isRemoveable($replaces[$name], $required, $replaces);
		}

		return false;
	}

	static public function isRemoveRequested($name, array $required, array $replaces)
	{
		if ($name == 'contao-community-alliance/composer') {
			return false;
		}

		if (isset($required[$name])) {
			return false;
		}

		if (isset($replaces[$name]) && $replaces[$name] != $name) {
			return static::isRemoveRequested($replaces[$name], $required, $replaces);
		}

		return true;
	}

	static public function getRequireConstraint($name, $require, $replaces)
	{
		if (isset($require[$name])) {
			return $require[$name];
		}

		if (isset($replaces[$name]) && $replaces[$name] != $name) {
			return static::getRequireConstraint($replaces[$name], $require, $replaces);
		}

		return null;
	}

	static public function getRequirePackageName($name, $require, $replaces)
	{
		if (isset($require[$name])) {
			return $name;
		}

		if (isset($replaces[$name]) && $replaces[$name] != $name) {
			return static::getRequirePackageName($replaces[$name], $require, $replaces);
		}

		return null;
	}
}
