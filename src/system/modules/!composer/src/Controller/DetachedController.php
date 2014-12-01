<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Installer;
use ContaoCommunityAlliance\Contao\Composer\ConsoleColorConverter;
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
            $outFile->renameTo(UpdatePackagesController::OUTPUT_FILE_PATHNAME);
            $pidFile->delete();
            $this->redirect('contao/main.php?do=composer&amp;update=database');
        } else {
            if ($isRunning && \Input::getInstance()->post('terminate')) {
                shell_exec(sprintf('kill %d', $pid));
                $this->reload();
            }
        }

        $converter = new ConsoleColorConverter();
        $output    = $converter->parse($output);

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
        } else {
            $template            = new \BackendTemplate('be_composer_client_detached');
            $template->output    = $output;
            $template->isRunning = $isRunning;
            $template->uptime    = $uptime;
            return $template->parse();
        }
    }
}
