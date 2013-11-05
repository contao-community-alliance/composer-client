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
 * Class Downloader
 *
 * Convenience class for downloading files.
 */
class Downloader
{
	/**
	 * Download an url and return or store contents.
	 *
	 * @param string $url
	 * @param bool   $file
	 *
	 * @return bool|null|string
	 * @throws \Exception
	 */
	static public function download($url, $file = false)
	{
		if (ini_get('allow_url_fopen')) {
			return static::fgetDownload($url, $file);
		}
		else if (function_exists('curl_init')) {
			return static::curlDownload($url, $file);
		}
		else {
			throw new \RuntimeException('No download mechanism available');
		}
	}

	/**
	 * @param      $url
	 * @param bool $file
	 *
	 * @return bool|null|string
	 * @throws \Exception
	 */
	static public function fgetDownload($url, $file = false)
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
				return static::fgetDownload($url, $file);
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
	 * @throws \Exception
	 */
	static public function curlDownload($url, $file = false)
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
			throw new \Exception(
				curl_error($curl),
				curl_errno($curl)
			);
		}

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code == 301 || $code == 302) {
			preg_match('/Location:(.*?)\n/', $header, $matches);
			$url = trim(array_pop($matches));

			return static::curlDownload($url, $file);
		}

		return $return;
	}
}
