<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
use Composer\Downloader\DownloadManager;
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
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;
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
use ContaoCommunityAlliance\Composer\Plugin\ConfigUpdateException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class DetachedController
 */
class DetachedController extends AbstractController
{
	const OUT_FILE_PATHNAME = 'system/tmp/composer.out';

	const PID_FILE_PATHNAME = 'system/tmp/composer.pid';

	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		$outFile = new \File(self::OUT_FILE_PATHNAME);
		$pidFile = new \File(self::PID_FILE_PATHNAME);

		$output = $outFile->getContent();
		$pid    = $pidFile->getContent();

		$isRunning = (bool) trim(shell_exec(sprintf('ps -p %d -o comm=', $pid)));

		$startTime = new \DateTime();
		$startTime->setTimestamp(filectime(TL_ROOT . '/' . self::PID_FILE_PATHNAME));

		$endTime = new \DateTime();
		$endTime->setTimestamp($isRunning ? time() : filemtime(TL_ROOT . '/' . self::OUT_FILE_PATHNAME));

		$uptime = $endTime->diff($startTime);
		$uptime = $uptime->format('%h h %I m %S s');

		if (!$isRunning && \Input::getInstance()->post('close')) {
			$outFile->delete();
			$pidFile->delete();
			$this->redirect('contao/main.php?do=composer&amp;update=database');
		}
		else if ($isRunning && \Input::getInstance()->post('terminate')) {
			shell_exec(sprintf('kill %d', $pid));
			$this->reload();
		}

		if (\Environment::getInstance()->isAjaxRequest) {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(
				array(
					'output'    => $output,
					'isRunning' => $isRunning,
					'uptime'    => $uptime,
				)
			);
			exit;
		}
		else {
			$template            = new \BackendTemplate('be_composer_client_detached');
			$template->output    = $output;
			$template->isRunning = $isRunning;
			$template->uptime    = $uptime;
			return $template->parse();
		}
	}
}
