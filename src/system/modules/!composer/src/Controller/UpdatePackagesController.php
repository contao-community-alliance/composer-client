<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Downloader\DownloadManager;
use Composer\Installer;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;
use ContaoCommunityAlliance\Composer\Plugin\ConfigUpdateException;
use ContaoCommunityAlliance\Composer\Plugin\DuplicateContaoException;
use ContaoCommunityAlliance\Contao\Composer\Util\FunctionAvailabilityCheck;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class UpdatePackagesController
 */
class UpdatePackagesController extends AbstractController
{
    const OUTPUT_FILE_PATHNAME = 'composer/composer.out';

    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        try {
            $packages = $input->post('packages') ?: $input->get('packages');
            $packages = explode(',', $packages);
            $packages = array_filter($packages);
            $dryRun   = $input->get('dry-run') || $input->post('dry-run');

            $mode = $this->determineRuntimeMode();

            switch ($mode) {
                case 'inline':
                    $this->runInline($packages, $dryRun);
                    break;

                case 'process':
                    $this->runProcess($packages, $dryRun);
                    break;

                case 'detached':
                    $this->runDetached($packages, $dryRun);
                    break;
            }
        } catch (DuplicateContaoException $e) {
            if (isset($_SESSION['COMPOSER_DUPLICATE_CONTAO_EXCEPTION'])
                && $_SESSION['COMPOSER_DUPLICATE_CONTAO_EXCEPTION']
            ) {
                unset($_SESSION['COMPOSER_DUPLICATE_CONTAO_EXCEPTION']);
                do {
                    Messages::addError(str_replace(TL_ROOT, '', $e->getMessage()));
                    $e = $e->getPrevious();
                } while ($e);
                $this->redirect('contao/main.php?do=composer');
            } else {
                $_SESSION['COMPOSER_DUPLICATE_CONTAO_EXCEPTION'] = true;
                $this->reload();
            }
        } catch (ConfigUpdateException $e) {
            do {
                Messages::addConfirmation(str_replace(TL_ROOT, '', $e->getMessage()));
                $e = $e->getPrevious();
            } while ($e);
            $this->reload();
        } catch (\RuntimeException $e) {
            do {
                Messages::addError(str_replace(TL_ROOT, '', $e->getMessage()));
                $e = $e->getPrevious();
            } while ($e);
            $this->redirect('contao/main.php?do=composer');
        }
    }

    protected function runInline($packages, $dryRun)
    {
        // disable all hooks
        $GLOBALS['TL_HOOKS'] = array();

        $lockPathname = preg_replace('#\.json$#', '.lock', $this->configPathname);

        /** @var DownloadManager $downloadManager */
        $downloadManager = $this->composer->getDownloadManager();
        $downloadManager->setOutputProgress(false);

        $argv = array(false, 'update');
        if ($dryRun) {
            $argv[] = '--dry-run';
        }
        if ($packages) {
            $argv = array_merge($argv, $packages);
        }

        $outputStream = fopen('php://memory', 'rw');
        $argvInput    = new ArgvInput($argv);
        $streamOutput = new StreamOutput($outputStream);

        $commandEvent = new CommandEvent(PluginEvents::COMMAND, 'update', $argvInput, $streamOutput);
        $this->composer
            ->getEventDispatcher()
            ->dispatch($commandEvent->getName(), $commandEvent);

        $installer = Installer::create($this->io, $this->composer);
        $installer->setDryRun($dryRun);
        $installer->setUpdateWhitelist($packages);
        $installer->setWhitelistDependencies(true);

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

        try {
            $installer->run();
        } catch (\Exception $e) {
            $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
            throw $e;
        }

        $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();
        file_put_contents(TL_ROOT . '/' . self::OUTPUT_FILE_PATHNAME, $_SESSION['COMPOSER_OUTPUT']);

        // redirect to database update
        $this->redirect('contao/main.php?do=composer&update=database');
    }

    private function buildCmd($packages, $dryRun)
    {
        $cmd = sprintf(
            '%s composer.phar update --no-ansi --no-interaction',
            $GLOBALS['TL_CONFIG']['composerPhpPath']
        );

        if ($dryRun) {
            $cmd .= ' --dry-run';
        }

        if ($packages) {
            $cmd .= ' --with-dependencies ' . implode(' ', array_map('escapeshellarg', $packages));
        }

        switch ($GLOBALS['TL_CONFIG']['composerVerbosity']) {
            case 'VERBOSITY_QUIET':
                $cmd .= ' --quiet';
                break;
            case 'VERBOSITY_VERBOSE':
                $cmd .= ' -v';
                break;
            case 'VERBOSITY_VERY_VERBOSE':
                $cmd .= ' -vv';
                break;
            case 'VERBOSITY_DEBUG':
                $cmd .= ' -vvv';
                break;
            default:
        }

        if ($GLOBALS['TL_CONFIG']['composerProfiling']) {
            $cmd .= ' --profile';
        }

        return $cmd;
    }

    protected function runProcess($packages, $dryRun)
    {
        // disable all hooks
        $GLOBALS['TL_HOOKS'] = array();

        $cmd          = $this->buildCmd($packages, $dryRun);
        $inputStream  = fopen('php://temp', 'r');
        $outputStream = fopen('php://temp', 'rw');
        $pipes        = array();

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
        file_put_contents(TL_ROOT . '/' . self::OUTPUT_FILE_PATHNAME, $_SESSION['COMPOSER_OUTPUT']);

        fclose($inputStream);
        fclose($outputStream);

        // redirect to database update
        $this->redirect('contao/main.php?do=composer&update=database');
    }

    protected function runDetached($packages, $dryRun)
    {
        $cmd = $this->buildCmd($packages, $dryRun);

        file_put_contents(TL_ROOT . '/' . DetachedController::OUT_FILE_PATHNAME, '$ ' . $cmd . PHP_EOL);

        $cmd .= sprintf(
            ' >> %s 2>&1 & echo $!',
            escapeshellarg(TL_ROOT . '/' . DetachedController::OUT_FILE_PATHNAME)
        );

        $processId = shell_exec($cmd);

        $pidFile = new \File(DetachedController::PID_FILE_PATHNAME);
        $pidFile->write(trim($processId));
        $pidFile->close();

        // redirect to database update
        $this->redirect('contao/main.php?do=composer');
    }

    /**
     * Determine the runtime mode to use depending on the availability of functions.
     *
     * @return mixed
     */
    private function determineRuntimeMode()
    {
        $mode = $GLOBALS['TL_CONFIG']['composerExecutionMode'];
        if ('inline' === $mode) {
            return $mode;
        }
        $functions = array();

        if ($mode === 'process') {
            $functions = array('proc_open', 'proc_close');
        }
        if ($mode === 'detached') {
            $functions = array('shell_exec');
            if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
                $functions[] = 'posix_kill';
            }
        }

        foreach ($functions as $function) {
            if (!FunctionAvailabilityCheck::isFunctionEnabled($function)) {
                Messages::addWarning($function . ' is disabled, reverting from ' . $mode . ' to inline execution');
                return 'inline';
            }
        }

        return $mode;
    }
}
