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
 * Class UpdatePackagesController
 */
class UpdatePackagesController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		try {
			switch ($GLOBALS['TL_CONFIG']['composerExecutionMode']) {
				case 'inline':
					$this->runInline();
					break;

				case 'process':
					$this->runProcess();
					break;

				case 'detached':
					$this->runDetached();
					break;
			}
		}
		catch (ConfigUpdateException $e) {
			do {
				$_SESSION['TL_CONFIRM'][] = str_replace(TL_ROOT, '', $e->getMessage());
				$e                        = $e->getPrevious();
			}
			while ($e);
			$_SESSION['TL_INFO'][] = $GLOBALS['TL_LANG']['composer_client']['restartOperation'];
			$this->redirect('contao/main.php?do=composer');
		}
		catch (\RuntimeException $e) {
			do {
				$_SESSION['TL_ERROR'][] = str_replace(TL_ROOT, '', $e->getMessage());
				$e                      = $e->getPrevious();
			}
			while ($e);
			$this->redirect('contao/main.php?do=composer');
		}
	}

	protected function runInline()
	{
		// disable all hooks
		$GLOBALS['TL_HOOKS'] = array();

		if (version_compare(VERSION, '3', '<')) {
			spl_autoload_unregister('__autoload');
		}

		$lockPathname = preg_replace('#\.json$#', '.lock', $this->configPathname);

		/** @var DownloadManager $downloadManager */
		$downloadManager = $this->composer->getDownloadManager();
		$downloadManager->setOutputProgress(false);

		$outputStream = fopen('php://memory', 'rw');
		$argvInput    = new ArgvInput(array(false, 'update'));
		$streamOutput = new StreamOutput($outputStream);

		$commandEvent = new CommandEvent(PluginEvents::COMMAND, 'update', $argvInput, $streamOutput);
		$this->composer
			->getEventDispatcher()
			->dispatch($commandEvent->getName(), $commandEvent);

		$installer = Installer::create($this->io, $this->composer);

		switch ($this->composer->getConfig()
			->get('preferred-install')) {
			case 'source':
				$installer->setPreferSource(true);
				break;
			case 'dist':
				$installer->setPreferDist(true);
				break;
			case 'auto':
			default:
				// noop
				break;
		}

		if (file_exists(TL_ROOT . '/' . $lockPathname)) {
			$installer->setUpdate(true);
		}

		$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

		// redirect to database update
		$this->redirect('contao/main.php?do=composer&update=database');
	}

	protected function runProcess()
	{
		// disable all hooks
		$GLOBALS['TL_HOOKS'] = array();

		$cmd = sprintf(
			'%s composer.phar update --no-ansi --no-interaction',
			$GLOBALS['TL_CONFIG']['composerPhpPath']
		);

		$inputStream = fopen('php://memory', 'r');
		$outputStream = fopen('php://memory', 'rw');
		$pipes = array();

		$proc = proc_open(
			$cmd,
			array(
				$inputStream,
				$outputStream,
				$outputStream,
			),
			$pipes,
			TL_ROOT . '/composer'
		);

		if ($proc === false) {
			throw new \RuntimeException('Could not execute ' . $cmd);
		}

		proc_close($proc);

		fseek($outputStream, 0);
		$_SESSION['COMPOSER_OUTPUT'] .= stream_get_contents($outputStream);

		fclose($inputStream);
		fclose($outputStream);

		// redirect to database update
		$this->redirect('contao/main.php?do=composer&update=database');
	}

	protected function runDetached()
	{
        $cmd = sprintf(
            '%s composer.phar update --no-ansi --no-interaction > %s 2>&1 & echo $!',
            $GLOBALS['TL_CONFIG']['composerPhpPath'],
            escapeshellarg(TL_ROOT . '/' . DetachedController::OUT_FILE_PATHNAME)
        );

        $processId = shell_exec($cmd);

        $pidFile = new \File(DetachedController::PID_FILE_PATHNAME);
        $pidFile->write(trim($processId));
        $pidFile->close();

        // redirect to database update
        $this->redirect('contao/main.php?do=composer');
	}
}
